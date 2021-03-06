<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:xs="http://www.w3.org/2001/XMLSchema"
xmlns:exsl="http://exslt.org/common"
xmlns:m="http://www.w3.org/1998/Math/MathML"
extension-element-prefixes="exsl">
<xsl:include href="calstable.xslt"/>
<xsl:variable name="parent" select="'#'"/>
<xsl:variable name="URLbase" select="string('http://demonstrator.webhop.org/MathUnited/')"/>
<xsl:variable name="contentPrefix" select="string('http://demonstrator.webhop.org:8080/MathUnited/view?variant=2&amp;item=introduction&amp;parent=viewmethod%3fref%3dmethod-m4a.xml%26variant%3dm4a&amp;ref=')"/>
<xsl:output method="html" doctype-system="http://www.w3.org/TR/html4/strict.dtd" doctype-public="-//W3C//DTD HTML 4.01//EN"
indent="yes" />

<!--   **************** -->
<!--   START PROCESSING -->
<!--   **************** -->
<xsl:template match="/" name="main" >
<html  xmlns:m="http://www.w3.org/1998/Math/MathML">
<head>
   <title><xsl:value-of select="method/title"/></title>
   <script type="text/javascript" src="http://www.math4all.nl/MathJax/MathJax.js"></script>
   <script src="http://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js"
           djConfig="parseOnLoad: true">
   </script>
   <link rel="stylesheet" href="css/M4AStijl2.css" type="text/css"/>
</head>
<body>
<div class="shadow-ul"/>
<div class="pageDiv">
<div class="headingDiv">
    <div class="leftDiv headingContentDiv">
        <img class="logo" src="http://www.math4all.nl/MathAdore/Images/LogoMAThADORE.gif" align="middle" width="57" height="33" border="0"/>
        <xsl:value-of select="method/title"/>
    </div>
    <div class="menuDiv menu-item-div">
        <a class="navigatie">
           <xsl:attribute name="href"><xsl:value-of select="$parent"/></xsl:attribute>Overzicht
        </a>
    </div>
</div>
<div style="clear:both"></div>
<div class="sectionDiv">
   <div class="balkLinks"/>
   <div class="balk">
       <div class="section-container">
           <xsl:value-of select="method/title"/>
       </div>
   </div>
   <div class="balkRechts"/>
</div>
<div class="contentDiv">
<div class="leftDiv">
    <xsl:for-each select="method/components/component">
        <xsl:apply-templates select="."/>
    </xsl:for-each>
</div>
<div class="menuDiv">
</div>
</div>
</div>
<div class="shadow-lr"/>
</body>
</html>
</xsl:template>

<xsl:template match="component">
    <div class="component-container">
        <xsl:value-of select="title"/>
            <xsl:if test="not(starts-with(file, '#'))">
                <xsl:variable name="subcomponents" select = "document(file)"/>
                <xsl:for-each select="$subcomponents/component/subcomponents/subcomponent">
                   <a class="component-link">
                       <xsl:attribute name="href"><xsl:value-of select="concat($contentPrefix,file)"/></xsl:attribute>
                       <xsl:value-of select="title"/>
                   </a>
                </xsl:for-each>
            </xsl:if>
    </div>
</xsl:template>

</xsl:stylesheet>
