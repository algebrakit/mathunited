package nl.math4all.gae_m4a;

import org.apache.commons.io.IOUtils;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.*;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.Enumeration;

public class AkitProxy extends HttpServlet {
    static final String API_KEY = "YWxnZWJyYWtpdC5hbGdlYnJha2l0LXdlYnNpdGUuMzkyYTBiNTU4YjFjYjY0Mzg3ZGVlYWJhNmM5M2I2MjM4OWEzNjdmMWUyNjgxY2I3OWM5MjRkODkwN2U5YjJmYmFjZDNlOWQxMmZkYWRjMzc4NWUzYzJiM2RmYmU5YTNi";


    private final static Logger LOGGER = Logger.getLogger(AkitProxy.class.getName());
    private static final long serialVersionUID = 1L;
    static {
        LOGGER.setLevel(Level.INFO);
    }
//    static final String AKIT_BASE = "https://api.algebrakit.com";
    static final String AKIT_BASE = "https://api.algebrakit.com";

    @Override
    public void doOptions(HttpServletRequest req, HttpServletResponse resp)
            throws IOException {
        //The following are CORS headers. Max age informs the
        //browser to keep the results of this call for 1 day.
        resp.setHeader("Access-Control-Allow-Origin", "*");
        resp.setHeader("Access-Control-Allow-Methods", "GET, POST");
        resp.setHeader("Access-Control-Allow-Headers", "Content-Type");
        resp.setHeader("Access-Control-Max-Age", "86400");
        //Tell the browser what requests we allow.
        resp.setHeader("Allow", "GET, HEAD, POST, TRACE, OPTIONS");
    }

    @Override
    public void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        try {
            URL url =getUrl(request);
            HttpURLConnection con = (HttpURLConnection) url.openConnection();

            Enumeration headers = request.getHeaderNames();
            while(headers.hasMoreElements()) {
                String name = (String)headers.nextElement();
                String value = request.getHeader(name);
                con.setRequestProperty(name, value);
            }

            con.setRequestMethod("GET");
            con.setUseCaches(false);
            con.setConnectTimeout(5000);
            con.setReadTimeout(5000);

            setCors(response);
            int status = con.getResponseCode();
            if(status!=HttpURLConnection.HTTP_OK) {
                LOGGER.warning("Proxy GET: response status = "+status + ", url = "+url.toString());
                response.sendError(status);
            } else {
                setCors(response);
                IOUtils.copy(con.getInputStream(),response.getOutputStream());
            }

        } catch(Exception e) {
            e.printStackTrace();
            Writer w = response.getWriter();
            PrintWriter pw = new PrintWriter(w);
            pw.println("error: " + e.getMessage());
            throw new ServletException(e);
        }

    }

    @Override
    public void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        try (BufferedReader br = new BufferedReader(new InputStreamReader(request.getInputStream(), "UTF-8"))) {

                StringBuilder buf = new StringBuilder();
                String line = br.readLine();
                while(line!=null) {
                    buf.append(line);
                    line = br.readLine();
                }
                String inputStr = buf.toString();

                URL url =getUrl(request);
                HttpURLConnection con = (HttpURLConnection) url.openConnection();

                Enumeration headers = request.getHeaderNames();
                while(headers.hasMoreElements()) {
                    String name = (String)headers.nextElement();
                    String value = request.getHeader(name);
                    con.setRequestProperty(name, value);
                }
                con.setRequestMethod("POST");
                con.setUseCaches(false);
                con.setRequestProperty("x-api-key", API_KEY);
                con.setConnectTimeout(5000);
                con.setReadTimeout(5000);
                con.setDoOutput(true);
                DataOutputStream out = new DataOutputStream(con.getOutputStream());
                out.writeBytes(inputStr);
                out.flush();
                out.close();

                int status = con.getResponseCode();
                if(status!=HttpURLConnection.HTTP_OK) {
                    LOGGER.warning("Proxy POST: response status = "+status + ", url = "+url.toString()+ ", input = "+inputStr.toString());
                    response.sendError(status);
                } else {
                    setCors(response);
                    IOUtils.copy(con.getInputStream(),response.getOutputStream());
                }
        } catch (Exception e) {
            e.printStackTrace();
            Writer w = response.getWriter();
            PrintWriter pw = new PrintWriter(w);
            pw.println("error: " + e.getMessage());
            throw new ServletException(e);
        }
    }


    private URL getUrl(HttpServletRequest request) throws MalformedURLException {
        String requestUrl = request.getRequestURL().toString();
        int index = requestUrl.lastIndexOf("/algebrakit/");
        // e.g. "/exercise/generate
        String akitPath = requestUrl.substring(index+"/algebrakit".length());
        String akitUrl = AKIT_BASE + akitPath;
        return new URL(akitUrl);
    }

    private void setCors(HttpServletResponse response) {
        response.setContentType("application/json; charset=utf-8");
        response.setHeader("Access-Control-Allow-Origin", "*");
        response.setHeader("Access-Control-Allow-Methods", "GET, POST");
        response.setHeader("Access-Control-Allow-Headers", "Content-Type");
        response.setHeader("Access-Control-Max-Age", "86400");
        //Tell the browser what requests we allow.
        response.setHeader("Allow", "GET, HEAD, POST, TRACE, OPTIONS");
    }

}
