package nl.math4all.mathunited;

import nl.math4all.mathunited.configuration.Component;
import java.io.*;
import java.net.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.util.logging.Logger;
import java.util.logging.Level;
import java.util.Map;
import java.util.HashMap;
import nl.math4all.mathunited.resolvers.ContentResolver;
import javax.xml.bind.DatatypeConverter;


//mathunited.pragma-ade.nl/MathUnited/view?variant=basis&comp=m4a/xml/12hv-me0&subcomp=3&item=explore
// - fixed parameters: variant, comp (component), subcomp (subcomponent).
// - other parameters are just passed to xslt

public class GeogebraGenerator extends HttpServlet {
    static final byte[] EOL = {(byte)'\r', (byte)'\n' };
    private final static Logger LOGGER = Logger.getLogger(GeogebraGenerator.class.getName());
    XSLTbean processor;
    Map<String, String> variantMap = new HashMap<String,String>();
    Map<String, Component> componentMap;
//    String ggbSource = "http://www.geogebra.org/web/4.2/web/web.nocache.js";
    String ggbSource = "http://js.geogebra.at/web/web.nocache.js";

    @Override
    public void init(ServletConfig config) throws ServletException {
         super.init(config);
    }


    @Override
    public void doGet (  HttpServletRequest request,
                         HttpServletResponse response)
             throws ServletException, IOException {

        Writer w = response.getWriter();
        PrintWriter pw = new PrintWriter(w);
        try{
            //read components. To be moved to init()
            String baseURL = "http://127.0.0.1/data/";
            String fname = request.getParameter("file");
            if(fname==null) {
                throw new Exception("Please supply a filename");
            }
            URL url = new URL(baseURL+fname);
            URLConnection conn = url.openConnection();
            int length = conn.getContentLength();
            byte[] b = new byte[length];
            InputStream is = url.openStream();
            int numread = is.read(b);
            is.close();
            String b64 = DatatypeConverter.printBase64Binary(b);
            response.setContentType("text/html");
            pw.println("<html style='overflow:hidden'><head><style type='text/css'><!--body { font-family:Arial,Helvetica,sans-serif; margin-left:40px }--></style><script type='text/javascript' language='javascript' src='"+ggbSource+"'></script></head>");
            pw.println("<body><article class='geogebraweb' style='display:inline-block;' data-param-ggbbase64='"+b64+"'></article>");
            pw.println("<script type='text/javascript'>var ggbApplet = document.ggbApplet;function ggbOnInit() {}</script></body></html>");
        } catch(Exception e) {
            pw.println("An error occured: "+e.getMessage());
            e.printStackTrace();
        }
    }



    @Override
    public void doPost (  HttpServletRequest request,
                         HttpServletResponse response)
             throws ServletException, IOException {
        //get the pdf from the session and return it
    }

}