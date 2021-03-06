package nl.math4all.mathunited.configuration;

import java.util.List;
import java.util.ArrayList;
import java.util.Map;
import java.util.HashMap;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.xpath.*;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;
import org.xml.sax.InputSource;


public class Component {
    public List<SubComponent> subComponentList;
    String id;
    public String title;
    public String subTitle;
    public String methodId;
    public String number;
    public String compfile;

    public Component(String id, String methodId, String title, String compfile, String subTitle, List<SubComponent> subs){
        this.id = id;
        this.subComponentList = subs;
        this.title = title;
        this.compfile = compfile;
        if(subTitle == null) this.subTitle = "";
        else  this.subTitle = subTitle;
        this.methodId = methodId;
    }

    public String toString() {
        return "(Component "+id+")";
    }
    
    static Map<String, Component> getComponentMap(InputSource xmlSource ) throws Exception {
        Map<String, Component> componentMap = new HashMap<String, Component>();

        DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
        DocumentBuilder dBuilder = dbFactory.newDocumentBuilder();
        XPathFactory xpathFactory = XPathFactory.newInstance();
        XPath xpath = xpathFactory.newXPath();

        Document doc = dBuilder.parse(xmlSource);
        doc.getDocumentElement().normalize();

        XPathExpression expr = xpath.compile("//method");
        XPathExpression exprID = xpath.compile("@id");
        XPathExpression exprComp = xpath.compile("components/component");

        NodeList methodList = (NodeList)expr.evaluate(doc, XPathConstants.NODESET);

        for(int ii=0; ii < methodList.getLength(); ii++) {
        	Node methodNode = methodList.item(ii);
        	String methodId = (String)exprID.evaluate(methodNode, XPathConstants.STRING);
        	NodeList componentsList = (NodeList)exprComp.evaluate(methodNode, XPathConstants.NODESET);

            for (int jj = 0; jj < componentsList.getLength(); jj++) {
                Node componentNode = componentsList.item(jj);
                Component comp = readComponent(methodId, componentNode, xpath);
                componentMap.put(comp.id, comp);
            }
        }

        return componentMap;
    }

    static public Component readComponent(String methodId, Node parent, XPath xpath) throws Exception {
        List<SubComponent> subList = new ArrayList<SubComponent>();
        XPathExpression expr = xpath.compile("@id");
        String compId = (String)expr.evaluate(parent, XPathConstants.STRING);

        expr = xpath.compile("title");
        String comptitle = (String)expr.evaluate(parent, XPathConstants.STRING);

        expr = xpath.compile("@file");
        String compfile = (String)expr.evaluate(parent, XPathConstants.STRING);

        expr = xpath.compile("subtitle");
        String compSubTitle = (String)expr.evaluate(parent, XPathConstants.STRING);

        expr = xpath.compile("@number");
        String compNumber = (String)expr.evaluate(parent, XPathConstants.STRING);

        Component comp = new Component(compId, methodId, comptitle, compfile, compSubTitle, subList);
        comp.number = compNumber;

        expr = xpath.compile("subcomponents/subcomponent");
        NodeList subsList = (NodeList)expr.evaluate(parent, XPathConstants.NODESET);

        XPathExpression exprFile = xpath.compile("file");
        XPathExpression exprTitle = xpath.compile("title");
        XPathExpression exprID = xpath.compile("@id");
        XPathExpression exprNumber = xpath.compile("@number");

        for (int ii = 0; ii < subsList.getLength(); ii++) {
            Node subNode = subsList.item(ii);

            //expr = xpath.compile("file");
            String subfile = (String) exprFile.evaluate(subNode, XPathConstants.STRING);

            //expr = xpath.compile("title");
            String subtitle = (String) exprTitle.evaluate(subNode, XPathConstants.STRING);

            //expr = xpath.compile("@id");
            String subId = (String) exprID.evaluate(subNode, XPathConstants.STRING);

            //expr = xpath.compile("@number");
            String subNumber = (String) exprNumber.evaluate(subNode, XPathConstants.STRING);

            SubComponent sub = new SubComponent(subId, subtitle, subfile, subNumber);

            subList.add(sub);
        }

        return comp;
    }
    
    public String getXML() {
        StringBuilder sb = new StringBuilder();
        sb.append("<component id=\"").append(id).append("\" number=\"").append(number).append("\" file=\"").append(compfile).append("\"><title>").append(title).append("</title>");
        sb.append("<subtitle>").append(subTitle).append("</subtitle><subcomponents>");
        for(SubComponent sc : subComponentList) {
            sb.append("<subcomponent id=\"").append(sc.id).append("\" number=\"").append(sc.number).append("\">");
            sb.append("<title>").append(sc.title).append("</title>");
            sb.append("</subcomponent>");
        }
        sb.append("</subcomponents></component>");
        return sb.toString();
    }

	public void addToParameterMap(Map<String, String> parameterMap, String subComp) {
        parameterMap.put("component_id", id);
        parameterMap.put("component_number", number);
        parameterMap.put("component_file", compfile);
        parameterMap.put("component_title", title);
        parameterMap.put("component_subtitle", subTitle);
        parameterMap.put("subcomponent_count", Integer.toString(subComponentList.size()));
        int index = 0;
        String precId = "";
        for(SubComponent sc : subComponentList) {
        	if (sc.id.equals(subComp)) {
                parameterMap.put("subcomponent_number", sc.number);
                parameterMap.put("subcomponent_title", sc.title);
                parameterMap.put("subcomponent_id", sc.id);
                parameterMap.put("subcomponent_index", Integer.toString(index));
                break;
        	}
        	else 
        		precId = sc.id;
        	index++;
        }
        String nextId = "";
        if (index + 1 < subComponentList.size())
            nextId = subComponentList.get(index + 1).id;
        parameterMap.put("subcomponent_preceding_id", precId);
        parameterMap.put("subcomponent_following_id", nextId);
	}

}

