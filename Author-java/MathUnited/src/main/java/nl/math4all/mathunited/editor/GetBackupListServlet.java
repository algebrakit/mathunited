package nl.math4all.mathunited.editor;

import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.util.logging.Logger;
import java.util.logging.Level;
import java.util.Map;
import java.util.Properties;
import nl.math4all.mathunited.configuration.*;
import nl.math4all.mathunited.configuration.SubComponent;
import nl.math4all.mathunited.configuration.Component;
import nl.math4all.mathunited.utils.UserManager;
import nl.math4all.mathunited.utils.Utils;

//mathunited.pragma-ade.nl/MathUnited/view?variant=basis&comp=m4a/xml/12hv-me0&subcomp=3&item=explore
// - fixed parameters: variant, comp (component), subcomp (subcomponent).
// - other parameters are just passed to xslt

public class GetBackupListServlet extends HttpServlet {
    private final static Logger LOGGER = Logger.getLogger(GetBackupListServlet.class.getName());
    Map<String, Component> componentMap;
    Properties prop = new Properties();
    
    @Override
    public void init(ServletConfig config) throws ServletException {
        super.init(config);
        LOGGER.setLevel(Level.INFO);
    }

    @Override
    public void doGet (  HttpServletRequest request,
                         HttpServletResponse response)
             throws ServletException, IOException {

        try{
            Configuration config = Configuration.getInstance();

            UserSettings usettings = UserManager.isLoggedIn(request);
            Repository repository = Utils.getRepository(request);
            String comp = Utils.readParameter("comp", true, request);
            String subcomp = Utils.readParameter("subcomp", true, request);
            
            //read components. 
            componentMap = repository.readComponentMap();
            Component component = componentMap.get(comp);
            if(component==null) {
                throw new Exception("Er bestaat geen component met id '"+comp+"'");
            }

            // find subcomponent
            SubComponent sub=null;
            int subcomp_index = 0;
            for(subcomp_index=0; subcomp_index<component.subComponentList.size(); subcomp_index++ ){
                sub = component.subComponentList.get(subcomp_index);
                if(sub.id.equals(subcomp))  break;
            }
            if(sub==null) {
                throw new Exception("Er bestaat geen subcomponent met id '"+subcomp+"'");
            }
            
            int ind = sub.file.lastIndexOf('/');
            String backupbase = config.getContentRoot()+repository.getPath()+"/_history/"+sub.file.substring(0, ind+1);
            File logFile = new File(backupbase+"../log.xml");

            response.setContentType("application/xml");
            PrintWriter writer = response.getWriter();
            writer.println("<log-index>");
            if(logFile.exists()) {
                BufferedReader br=null;
                try {
                    br = new BufferedReader(new FileReader(logFile));
                    StringBuilder sb = new StringBuilder();
                    String line = br.readLine();

                    while (line != null) {
                        writer.println(line);
                        line = br.readLine();
                    }
                } catch(Exception e) {
                    LOGGER.severe("Error occurred: "+e.getMessage());
                } finally {
                    if(br!=null) br.close();
                }
            } 
            writer.println("</log-index>");
        }
        catch (Exception e) {
            System.out.println(Utils.echoContext(request, "ERROR"));
            Utils.writeError(response, e);
        }
    }
    
    @Override
    public void doPost (  HttpServletRequest request,
                         HttpServletResponse response)
             throws ServletException, IOException {
        //get the pdf from the session and return it
    }

}