package nl.math4all.mathunited.editor;

import java.io.*;
import java.net.URLDecoder;
import javax.servlet.*;
import javax.servlet.http.*;
import java.util.logging.Logger;
import java.util.logging.Level;
import java.util.Map;
import java.util.HashMap;
import javax.xml.transform.sax.SAXSource;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;
import org.w3c.dom.Node;
import javax.xml.xpath.*;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.NodeList;
import java.nio.channels.FileChannel;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.Document;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSSerializer;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.DOMConfiguration;
import org.w3c.dom.DOMError;
import org.w3c.dom.DOMErrorHandler;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;
import nl.math4all.mathunited.XSLTbean;
import nl.math4all.mathunited.exceptions.LoginException;
import nl.math4all.mathunited.configuration.*;
import nl.math4all.mathunited.configuration.SubComponent;
import nl.math4all.mathunited.configuration.Component;
import nl.math4all.mathunited.resolvers.ContentResolver;
import nl.math4all.mathunited.utils.UserManager;
import nl.math4all.mathunited.utils.FileManager;

//mathunited.pragma-ade.nl/MathUnited/view?variant=basis&comp=m4a/xml/12hv-me0&subcomp=3&item=explore
// - fixed parameters: variant, comp (component), subcomp (subcomponent).
// - other parameters are just passed to xslt

public class PostContentServlet extends HttpServlet {
    private final static Logger LOGGER = Logger.getLogger(PostContentServlet.class.getName());
    private String resultXML = "<post result=\"{#POSTRESULT}\"><message>{#MESSAGE}</message></post>";
    XSLTbean processor;
    Map<String, Component> componentMap;
    ServletContext context;

    @Override
    public void init(ServletConfig config) throws ServletException {
        try{
            super.init(config);
            LOGGER.setLevel(Level.FINE);
            context = getServletContext();

            processor = new XSLTbean(context);
        } catch(Exception e) {
            System.out.println(e.getMessage());
        }
    }


    @Override
    public void doPost ( HttpServletRequest request,
                         HttpServletResponse response) throws ServletException, IOException {

        request.setCharacterEncoding("UTF-8");
        response.setContentType("application/xml");
        Writer w = response.getWriter();
        PrintWriter pw = new PrintWriter(w);
        try{
            //check if user is logged in
            Configuration config = Configuration.getInstance();
            String repoId=null;
            Cookie[] cookieArr = request.getCookies();
            if(cookieArr != null) {
                for(Cookie c:cookieArr) {
                    String name = c.getName();
                    if(name.equals("REPO")) {
                        repoId = c.getValue();
                    }
                }
            }
            if(repoId==null) {
                throw new Exception("Repository is not set.");
            }
            UserSettings usettings = UserManager.isLoggedIn(request,response);
/*
            //read request parameters
            Map<String, String[]> paramMap = request.getParameterMap();
*/
            BufferedReader br = request.getReader();
            String _repo = br.readLine(); //not used
            String comp = br.readLine();
            String subcomp = br.readLine();
            
            StringBuffer htmlBuffer = new StringBuffer();
            String sCurrentLine;
            while ((sCurrentLine = br.readLine()) != null) {
	         htmlBuffer.append(sCurrentLine);
            }
            String html = htmlBuffer.toString();
            
            if(comp==null) {
                throw new Exception("Het verplichte argument 'comp' ontbreekt.");
            } 
            if(subcomp==null) {
                throw new Exception("Het verplichte argument 'subcomp' ontbreekt.");
            } else {subcomp=subcomp.trim();}
            if(repoId==null) {
                throw new Exception("Het verplichte argument 'repo' ontbreekt.");
            }
            if(html==null) {
                throw new Exception("Het verplichte argument 'html' ontbreekt.");
            }
            Map<String, String> parameterMap = new HashMap<String, String>();
            parameterMap.put("comp", comp);
            parameterMap.put("subcomp", subcomp);
            parameterMap.put("repo", repoId);
            parameterMap.put("html", html);
            
            LOGGER.info("Commit: user="+usettings.mail+", comp="+comp+", subcomp="+subcomp+", repo="+repoId);
            
            Repository repository = config.getRepos().get(repoId);
            if(repository==null) {
                throw new Exception(repoId+" is een ongeldige repository");
            }
            boolean access = false;
            for(String role : usettings.roles) {
                if(role.equals(repository.edit_permission)) {
                    access = true;
                    break;
                }
            }
            if(!access) throw new LoginException("You do not have the rights to edit repository "+repoId);

            //user has access, continue

            //read components. To be moved to init()
            File f = new File(config.contentRoot+repository.path+"/leerlijnen/components.xml");
            if(!f.exists() && !repository.baseRepo.isEmpty()) {
                Repository baseRepo = config.getRepos().get(repository.baseRepo);
                f = new File(config.contentRoot+baseRepo.path+"/leerlijnen/components.xml");
            }
            FileInputStream is = new FileInputStream(f);
            componentMap = Component.getComponentMap(new InputSource(is));
            
            Component component = componentMap.get(comp);
            if(component==null) {
                throw new Exception("Er bestaat geen component met id '"+comp+"'");
            }

            // find subcomponent, previous and following
            SubComponent sub=null;
            boolean found = false;
            for(int ii=0; ii<component.subComponentList.size(); ii++ ){
                sub = component.subComponentList.get(ii);
                if(sub.id.equals(subcomp)) {
                    found=true;
                    break;
                }
            }
            
            if(!found) {
                throw new Exception("Er bestaat geen subcomponent met id '"+subcomp+"'");
            }

            LOGGER.fine("found subcomponent "+subcomp+": "+sub.title);
            XMLReader xmlReader = XMLReaderFactory.createXMLReader("org.ccil.cowan.tagsoup.Parser");
            xmlReader.setFeature(org.ccil.cowan.tagsoup.Parser.namespacesFeature, false);
            xmlReader.setEntityResolver(ContentResolver.entityResolver);
            
            int ind = sub.file.lastIndexOf('/');
            String refbase = config.getContentRoot()+repository.path+"/"+sub.file.substring(0, ind+1);

            ContentResolver resolver = new ContentResolver(repoId, context);
            StringReader strReader = new StringReader(html);
            InputSource xmlSource = new InputSource(strReader);
            SAXSource xmlSaxSource = new SAXSource(xmlReader, xmlSource);

            Node root = processor.processToDOM(xmlSaxSource, "m4a_inverse", parameterMap, resolver);

            File subcompFile = new File(refbase);
            File zipFile = FileManager.backupSubcomponent(subcompFile, repository);
            FileManager.log(subcompFile.getParentFile(), usettings.username, zipFile, repository);
            
            XPath xpath = XPathFactory.newInstance().newXPath();
            String expression = "//include";
            NodeList nodes = (NodeList) xpath.evaluate(expression, root, XPathConstants.NODESET);
            int n = nodes.getLength();
            for(int ii=0; ii<n; ii++) {
                Node node = nodes.item(ii);
                NamedNodeMap nodeMap = node.getAttributes();
                if(nodeMap!=null) {
                    Node attrNode = nodeMap.getNamedItem("filename");
                    if(attrNode!=null) {
                        String fileStr = refbase + attrNode.getNodeValue();
                        if(node.getFirstChild()!=null) {
                            FileManager.writeToFile(fileStr, node.getFirstChild(), repository);
                            node.removeChild(node.getFirstChild());
                        } 
                    }
                }
            }
            //store master file
            expression = "/root/subcomponent";
            Node node = (Node) xpath.evaluate(expression, root, XPathConstants.NODE);
            String fileStr = config.getContentRoot()+repository.path+"/" + sub.file;
            FileManager.writeToFile(fileStr, node, repository);
            
            String result = resultXML.replace("{#POSTRESULT}","true").replace("{#MESSAGE}", "success");
            pw.println(result);
        }
        catch (Exception e) {
            e.printStackTrace();
            String result = resultXML.replace("{#POSTRESULT}","false").replace("{#MESSAGE}", e.getMessage());
            pw.println(result);
        }
        
    }
    

    @Override
    public void doGet (  HttpServletRequest request,
                         HttpServletResponse response)
             throws ServletException, IOException {

    }
    
    
}