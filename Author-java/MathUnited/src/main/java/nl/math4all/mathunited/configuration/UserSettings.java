package nl.math4all.mathunited.configuration;
/**
 *
 * @author martijnslob
 */
public class UserSettings {
    public String username;
    public String password;
    public java.util.List<String> roles;
    public String mail;
    public String repo;
    public long salt;
    private Clipboard clipboard;
    
    public UserSettings() {
        clipboard = new Clipboard();
    }

    public Clipboard getClipboard() {return clipboard;}
    
}
