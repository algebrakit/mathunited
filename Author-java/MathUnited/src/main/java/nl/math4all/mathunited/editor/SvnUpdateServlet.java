package nl.math4all.mathunited.editor;

import nl.math4all.mathunited.configuration.*;
import nl.math4all.mathunited.utils.ScriptRunner;
import nl.math4all.mathunited.utils.UserManager;
import nl.math4all.mathunited.utils.Utils;
import org.apache.commons.io.FileUtils;
import org.apache.commons.io.output.WriterOutputStream;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.*;
import java.util.*;
import java.util.concurrent.locks.Lock;
import java.util.concurrent.locks.ReentrantLock;
import java.util.logging.Level;
import java.util.logging.Logger;

//mathunited.pragma-ade.nl/MathUnited/view?variant=basis&comp=m4a/xml/12hv-me0&subcomp=3&item=explore
// - fixed parameters: variant, comp (component), subcomp (subcomponent).
// - other parameters are just passed to xslt

public class SvnUpdateServlet extends HttpServlet {

    private final static Logger LOGGER = Logger.getLogger(SvnUpdateServlet.class.getName());

    private static Lock lock = new ReentrantLock();

    private static File logFile;
    {
        try {
            logFile = File.createTempFile("svn-update", ".tmp");
            logFile.deleteOnExit();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
            throws ServletException, IOException
    {
        Configuration config = Configuration.getInstance();
        response.setContentType("text/plain");
        PrintWriter writer = response.getWriter();

        // Read request parameters
        Map<String, String> parameterMap = Utils.readParameters(request);

        // Force login
        try {
            UserSettings usettings = UserManager.isLoggedIn(request);
        } catch (Exception e) {
            writer.println("!!! NOT LOGGED IN");
            return;

        }

        if (lock.tryLock()) {
            try {

                String svnPath = config.getContentRoot();
                ScriptRunner runner = new ScriptRunner(writer);

                String fix = parameterMap.get("fix");
                if (Objects.equals(parameterMap.get("unlock"), "true")) {
                    runner.runScript("svn-force-unlock");
                } else if (Objects.equals(parameterMap.get("fix"), "true")) {
                    runner.runScript("svn-fix", svnPath, logFile.getPath());
                } else {
                    String path = parameterMap.get("path");
                    if (path != null && !path.isEmpty()) {
                        File newPath = new File(svnPath, path);
                        if (Utils.isSubDirectory(new File(svnPath), newPath)) {
                            svnPath = newPath.getCanonicalPath();
                        } else {
                            writer.println("=== ILLEGAL REPO PATH " + newPath);
                            throw new Exception("Illegal svn path " + newPath);
                        }
                    }
                    runner.runScript("svn-update", svnPath, logFile.getPath());
                }

            }
            catch (Exception e) {
                LOGGER.log(Level.WARNING, e.getMessage());
            }
            finally {
                lock.unlock();
            }
        } else {
            System.out.println(Utils.echoContext(request, "ERROR"));
            writer.println("!!! SVN-UPDATE on " + config.getContentRoot() + " is already in progress");
            writer.println("!!! Not doing anything");
            writer.println("--- Messages from svn update so far:");
            WriterOutputStream wos = new WriterOutputStream(writer);
            FileUtils.copyFile(logFile, wos);
        }
    }

    @Override
    public void doPost(HttpServletRequest request,
                       HttpServletResponse response)
            throws ServletException, IOException {
        doGet(request, response);
    }

}