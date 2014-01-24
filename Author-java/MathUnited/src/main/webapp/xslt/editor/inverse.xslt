<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="2.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:saxon="http://saxon.sf.net/"
xmlns:exsl="http://exslt.org/common"
xmlns:m="http://www.w3.org/1998/Math/MathML"
xmlns:cals="http://www.someplace.org/cals"
xmlns:xhtml="http://www.w3.org/1999/xhtml"
exclude-result-prefixes="saxon cals"
extension-element-prefixes="exsl">

<!-- cleanup: remove empty paragraph nodes -->
<xsl:template match="itemintro[not(p) or not(p/text())]" mode="cleanup"/>    
<xsl:template match="p[not(count(*)>0 or text())]" mode="cleanup"/>    
<xsl:template match="node() | @*" mode="cleanup">
    <xsl:copy>
        <xsl:apply-templates select="@* | node()" mode="cleanup"/>
    </xsl:copy>
</xsl:template>


<!-- editor-mode (default): copy all attributes (except tag-attribute) and text -->
<xsl:template match="@*" mode="editor">
    <xsl:if test="name()!='tag'">
        <xsl:copy/>
    </xsl:if>
</xsl:template>

<xsl:template match="text()" mode="editor"><xsl:value-of select="normalize-space()"/></xsl:template>

<!-- do not copy the elements that do not explicitly have the tag attribute -->
<xsl:template match="*[not(string-length(@tag)>0)]" mode="editor">
     <xsl:apply-templates select="*" mode="editor"/>            
</xsl:template>

<!-- elements with tag-attribuut: transform to indicated element -->
<xsl:template match="*[string-length(@tag)>0]" mode="editor">
    <xsl:element name="{@tag}">
        <xsl:for-each select="@*[name()!='tag']">
            <xsl:attribute name="{name()}" select="."/>
        </xsl:for-each>
        <xsl:apply-templates mode="editor"/>
    </xsl:element>
</xsl:template>


<!-- Elements that are treated in a nonstandard way -->
<xsl:template match="*[@tag='stepaligntable']" mode="editor">
    <stepaligntable>
        <xsl:apply-templates select=".//xhtml:tr[@tag='cells']" mode="editor"/>
    </stepaligntable>
</xsl:template>

<xsl:template match="*[@tag='cells']" mode="editor">
    <cells>
        <c1><xsl:apply-templates select="*[@tag='c1']//xhtml:p/node()" mode="paragraph"/></c1>
        <c2><xsl:apply-templates select="*[@tag='c2']//xhtml:p/node()" mode="paragraph"/></c2>
        <c3><xsl:apply-templates select="*[@tag='c3']//xhtml:p/node()" mode="paragraph"/></c3>
    </cells>
    <text><xsl:apply-templates select="*[@tag='text']//xhtml:p/node()" mode="paragraph"/></text>
</xsl:template>

<xsl:template match="table" mode="editor">
    <xsl:apply-templates select="." mode="paragraph"/>
</xsl:template>

<!-- PARAGRAPH WIDGET -->
<!-- PARAGRAPH MODE   -->
<xsl:template match="div[@class='paragraph-content'] | xhtml:div[@class='paragraph-content']" priority="2" mode="editor">
    <xsl:apply-templates mode="paragraph"/>
</xsl:template>

<!-- ASCIIMathML: -->
<xsl:template match="xhtml:span[@class='MathJax_Preview'] | span[@class='MathJax_Preview']" mode="paragraph"/>
<xsl:template match="xhtml:span[@class='MathJax'] | span[@class='MathJax']" mode="paragraph"/>
<xsl:template match="xhtml:span[@class='math-container'] | span[@class='math-container']" mode="paragraph">
    <xsl:apply-templates mode="editor"/>
</xsl:template>
<xsl:template match="xhtml:script | script" mode="paragraph"/>
<xsl:template match="xhtml:span[@class='am-container'] | span[@class='am-container']" mode="paragraph">
    <xsl:apply-templates mode="paragraph"/>
</xsl:template>
<!-- end of ASCIIMathML: -->


<xsl:template match="a | xhtml:a" mode="paragraph">
    <xsl:choose>
        <xsl:when test="class='dox'">
            <resourcelink>
                <xsl:for-each select="@*">
                    <xsl:choose>
                        <xsl:when test="name()='href'">
                            <xsl:attribute name="href"><xsl:value-of select="replace(.,'../dox/','')"/></xsl:attribute>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="{name()}">
                                <xsl:value-of select="."/>
                            </xsl:attribute>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
                <xsl:apply-templates mode="paragraph"/>
            </resourcelink>
        </xsl:when>
        <xsl:otherwise>
            <hyperlink>
                <xsl:apply-templates select="@*" mode="paragraph"/>
                <xsl:apply-templates mode="paragraph"/>
            </hyperlink>
        </xsl:otherwise>
        
    </xsl:choose>
</xsl:template>
<xsl:template match="ul | xhtml:ul" mode="paragraph">
    <itemize nr="4" type="packed">
        <xsl:apply-templates select="li | xhtml:li" mode="paragraph"/>
    </itemize>
</xsl:template>
<xsl:template match="ol | xhtml:ol" mode="paragraph">
    <itemize>
        <xsl:apply-templates select="li | xhtml:li" mode="paragraph"/>
    </itemize>
</xsl:template>
<xsl:template match="li | xhtml:li" mode="paragraph">
    <item><xsl:apply-templates mode="paragraph"/></item>
</xsl:template>
<xsl:template match="*[string-length(@tag)>0]" mode="paragraph">
    <xsl:element name="{@tag}">
        <xsl:for-each select="@*[name()!='tag']">
            <xsl:attribute name="{name()}" select="."/>
        </xsl:for-each>
        <xsl:apply-templates mode="paragraph"/>
    </xsl:element>
</xsl:template>
<xsl:template match="p | xhtml:p" mode="paragraph">
    <xsl:for-each select="img | xhtml:img">
        <xsl:apply-templates select="." mode="image"/>
    </xsl:for-each>
    <p><xsl:apply-templates mode="paragraph"/></p>
</xsl:template>
<xsl:template match="span | xhtml:span" mode="paragraph">
    <xsl:apply-templates mode="paragraph"/>
</xsl:template>
<xsl:template match="img | xhtml:img" mode="paragraph">
    <xsl:choose>
        <xsl:when test="not(parent::p) and not(parent::xhtml:p)">
            <xsl:apply-templates select="." mode="image"/>
        </xsl:when>
        <xsl:otherwise/>
    </xsl:choose>
</xsl:template>

<xsl:template match="@*[name()!='tag']" mode="paragraph">
    <xsl:copy/>
</xsl:template>
<xsl:template match="text()" mode="paragraph">
    <xsl:sequence select="replace(., '\s+', ' ', 'm')"/>
</xsl:template>


<xsl:template match="img[@class='paperfigure'] | xhtml:img[@class='paperfigure']" mode="image">
   <xsl:variable name="width" select="number(@width) div $cm2px"/>
    
    <paperfigure type='c' label='*' id='*'>
        <xsl:attribute name="location"><xsl:value-of select="@location"/></xsl:attribute>
        <caption></caption>
        <content>
            <resource>
                <name><xsl:value-of select="reverse(tokenize(@src,'/'))[1]"/></name>
                <id></id>
                <width><xsl:if test="$width>0"><xsl:value-of select="$width"/>cm</xsl:if></width>
                <height></height>
                <description><xsl:value-of select="@alt"/></description>
                <owner></owner>
            </resource>
        </content>
    </paperfigure>
</xsl:template>

</xsl:stylesheet>
