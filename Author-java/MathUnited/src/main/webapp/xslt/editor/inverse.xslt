<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="2.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:exsl="http://exslt.org/common"
                xmlns:m="http://www.w3.org/1998/Math/MathML"
                xmlns:cals="http://www.someplace.org/cals"
                xmlns:xhtml="http://www.w3.org/1999/xhtml"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                exclude-result-prefixes="xsi"
                extension-element-prefixes="exsl">

    <xsl:template match="xhtml:*" mode="editor-prepare">
        <xsl:element name="{local-name()}">
            <xsl:apply-templates select="@* | node()" mode="editor-prepare"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="node() | @*" mode="editor-prepare">
        <xsl:copy>
            <xsl:apply-templates select="@* | node()" mode="editor-prepare"/>
        </xsl:copy>
    </xsl:template>

    <!-- cleanup: remove empty paragraph nodes -->
    <xsl:template match="itemintro[not(p) or not(p/text())]" mode="cleanup"/>
    <xsl:template match="p[not(count(*)>0 or text())]" mode="cleanup"/>
    <!-- transform block into @medium attribute. Note that in main.xslt the @medium-attribute is converted into a block to enable support in editor -->
    <xsl:template match="block[count(*) = count(p)+count(paperfigure)+count(papertable)]" mode="cleanup">
        <xsl:variable name="medium" select="@medium"/>
        <xsl:for-each select="*">
            <xsl:element name="{name()}">
                <xsl:attribute name="medium" select="$medium"/>
                <xsl:apply-templates select="@* | node()" mode="cleanup"/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="node() | @*" mode="cleanup">
        <xsl:copy><xsl:apply-templates select="@* | node()" mode="cleanup"/></xsl:copy>
    </xsl:template>

    <!-- editor-mode (default): copy all attributes (except tag-attribute) and text -->
    <xsl:template match="@*" mode="editor">
        <xsl:if test="name()!='tag'">
            <xsl:copy/>
        </xsl:if>
    </xsl:template>

    <!-- Remove whitespace -->
    <xsl:template match="text()" mode="editor">
        <xsl:if test="position() &gt; 1"><xsl:text> </xsl:text></xsl:if>
        <xsl:value-of select="normalize-space()"/>
    </xsl:template>

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


    <!-- Match bron paragraphs -->
    <xsl:template match="*[@tag='bron']" mode="editor" priority="2">
        <p class='bron'><xsl:apply-templates mode="paragraph"/></p>
    </xsl:template>

    <!-- Elements that are treated in a nonstandard way -->
    <xsl:template match="*[@tag='stepaligntable']" mode="editor" priority="2">
        <stepaligntable>
            <xsl:apply-templates select=".//tr[@tag='cells']" mode="editor"/>
        </stepaligntable>
    </xsl:template>

    <xsl:template match="*[@tag='stepaligntable']" mode="paragraph" priority="2">
        <stepaligntable>
            <xsl:apply-templates select=".//tr[@tag='cells']" mode="paragraph"/>
        </stepaligntable>
    </xsl:template>

    <xsl:template match="*[@tag='cells']" mode="editor" priority="2">
        <cells>
            <c1><xsl:apply-templates select="*[@tag='c1']//p/node()" mode="paragraph"/></c1>
            <c2><xsl:apply-templates select="*[@tag='c2']//p/node()" mode="paragraph"/></c2>
            <c3><xsl:apply-templates select="*[@tag='c3']//p/node()" mode="paragraph"/></c3>
        </cells>
        <text><xsl:apply-templates select="*[@tag='text']//p/node()" mode="paragraph"/></text>
    </xsl:template>

    <xsl:template match="*[@tag='cells']" mode="paragraph" priority="2">
        <cells>
            <c1><xsl:apply-templates select="*[@tag='c1']//p/node()" mode="paragraph"/></c1>
            <c2><xsl:apply-templates select="*[@tag='c2']//p/node()" mode="paragraph"/></c2>
            <c3><xsl:apply-templates select="*[@tag='c3']//p/node()" mode="paragraph"/></c3>
        </cells>
        <text><xsl:apply-templates select="*[@tag='text']//p/node()" mode="paragraph"/></text>
    </xsl:template>


    <xsl:template match="table" mode="editor">
        <xsl:apply-templates select="." mode="paragraph"/>
    </xsl:template>

    <!-- PARAGRAPH WIDGET -->
    <!-- PARAGRAPH MODE   -->
    <xsl:template match="div[@class='paragraph-content']" priority="2" mode="editor">
        <xsl:apply-templates mode="paragraph"/>
    </xsl:template>
    <xsl:template match="div[@tag='subcaption']" priority="2" mode="editor">
        <subcaption>
            <xsl:apply-templates mode="paragraph"/>
        </subcaption>
    </xsl:template>
    
    <!-- ASCIIMathML: -->
    <xsl:template match="span[@class='MathJax_Preview']" mode="paragraph"/>
    <xsl:template match="span[@class='MathJax']" mode="paragraph"/>
    <xsl:template match="span[@class='math-container']" mode="paragraph">
        <xsl:apply-templates mode="editor"/>
    </xsl:template>
    <xsl:template match="span[@tag='m:math']" mode="editor">
        <!-- Default notation is in ASCIIMathML. Sometimes MathML is required to have more control.  -->
        <m:math prevent-am="true">
            <xsl:apply-templates mode="editor"/>
        </m:math>
    </xsl:template>
    <xsl:template match="script" mode="paragraph"/>
    <xsl:template match="span[@class='am-container']" mode="paragraph">
        <xsl:apply-templates select="span[@tag='am']" mode="paragraph"/>
    </xsl:template>

    <!-- end of ASCIIMathML: -->

    <xsl:template match="a" mode="paragraph">
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

    <xsl:template match="br" mode="paragraph"><br/></xsl:template>

    <xsl:template match="ul" mode="paragraph">
        <itemize number="1" type="packed">
            <xsl:apply-templates select="li" mode="paragraph"/>
        </itemize>
    </xsl:template>
    <xsl:template match="ol" mode="paragraph">
        <itemize number='n'>
            <xsl:apply-templates select="li" mode="paragraph"/>
        </itemize>
    </xsl:template>

    <!--
    <xsl:template match="table | xhtml:table | tbody | xhtml:tbody | tr | xhtml:tr | td | xhtml:td | th | xhtml:th" mode="paragraph">
        <xsl:copy>
            <xsl:apply-templates mode="paragraph"/>
        </xsl:copy>
    </xsl:template>
    -->
    <xsl:template match="li" mode="paragraph">
        <item>
            <xsl:apply-templates mode="paragraph"/>
        </item>
    </xsl:template>
    <xsl:template match="*[string-length(@tag)>0]" mode="paragraph">
        <xsl:element name="{@tag}">
            <xsl:for-each select="@*[name()!='tag']">
                <xsl:attribute name="{name()}" select="."/>
            </xsl:for-each>
            <xsl:apply-templates mode="paragraph"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="p" mode="paragraph">
        <xsl:for-each select="@*[name()!='tag']">
            <xsl:attribute name="{name()}" select="."/>
        </xsl:for-each>
        <xsl:for-each select="img">
            <xsl:apply-templates select="." mode="image"/>
        </xsl:for-each>
        <p><xsl:apply-templates mode="paragraph"/></p>
    </xsl:template>


    <xsl:template match="span" mode="paragraph">
        <xsl:apply-templates mode="paragraph"/>
    </xsl:template>

    <xsl:template match="img" mode="paragraph">
        <xsl:choose>
            <xsl:when test="not(parent::p)">
                <xsl:apply-templates select="." mode="image"/>
            </xsl:when>
            <xsl:otherwise/>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="@*[name()!='tag']" mode="paragraph">
        <xsl:copy/>
    </xsl:template>

    <!-- Surpress whitespace -->
    <xsl:template match="text()" mode="paragraph">
        <xsl:sequence select="replace(., '\s+', ' ', 'm')"/>
    </xsl:template>

    <xsl:template match="sub | sup | b | i" mode="paragraph">
        <xsl:element name="{name()}"><xsl:apply-templates select="node()" mode="paragraph"/></xsl:element>
    </xsl:template>

    <xsl:template match="img[@class='paperfigure']" mode="image">
        <!-- only use original width, stored as attribute 'paperwidth'
       <xsl:variable name="width" select="number(@width) div $cm2px"/>
         -->
        <paperfigure>
            <xsl:attribute name="type">
                <xsl:choose>
                    <xsl:when test="@figure_type">
                        <xsl:value-of select="@figure_type" />
                    </xsl:when>
                    <xsl:otherwise>c</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>

            <xsl:attribute name="label">
                <xsl:choose>
                    <xsl:when test="@figure_label">
                        <xsl:value-of select="@figure_label" />
                    </xsl:when>
                    <xsl:otherwise>*</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>

            <xsl:attribute name="id">
                <xsl:choose>
                    <xsl:when test="@figure_id">
                        <xsl:value-of select="@figure_id" />
                    </xsl:when>
                    <xsl:otherwise>*</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>

            <xsl:attribute name="location">
                <xsl:value-of select="@location"/>
            </xsl:attribute>

            <xsl:if test="@paperlocation">
                <xsl:attribute name="paperlocation" select="@paperlocation"/>
            </xsl:if>

            <xsl:if test="@reset">
                <xsl:attribute name="reset" select="@reset"/>
            </xsl:if>

            <caption><xsl:value-of select="@caption"/></caption>
            <content>
                <resource>
                    <name><xsl:value-of select="reverse(tokenize(@src,'/'))[1]"/></name>
                    <id><xsl:value-of select="@resource_id"/></id>
                    <width><xsl:if test="@paperwidth"><xsl:value-of select="@paperwidth"/></xsl:if></width>
                    <height><xsl:if test="@paperheight"><xsl:value-of select="@paperheight"/></xsl:if></height>
                    <description><xsl:value-of select="@alt"/></description>
                    <owner><xsl:value-of select="@owner"/></owner>
                </resource>
            </content>
        </paperfigure>
    </xsl:template>

    <xsl:template match="img[@class='inlinefigure']" mode="image">
        <xsl:apply-templates select="node()" mode="paragraph"/>
    </xsl:template>

    <xsl:template match="img[@class='inlinefigure']" mode="paragraph">
        <!-- only use original width, stored as attribute 'paperwidth'
       <xsl:variable name="width" select="number(@width) div $cm2px"/>
         -->
        <inlinefigure>
            <xsl:if test="@figure_type">
                <xsl:attribute name="type"><xsl:value-of select="@figure_type"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="@figure_id">
                <xsl:attribute name="id"><xsl:value-of select="@figure_id"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="@figure_label">
                <xsl:attribute name="label"><xsl:value-of select="@figure_label"/></xsl:attribute>
            </xsl:if>
            <content>
                <resource type="graphic">
                    <name><xsl:value-of select="reverse(tokenize(@src,'/'))[1]"/></name>
                    <id><xsl:value-of select="@resource_id"/></id>
                    <width><xsl:if test="@paperwidth"><xsl:value-of select="@paperwidth"/></xsl:if></width>
                    <height><xsl:if test="@paperheight"><xsl:value-of select="@paperheight"/></xsl:if></height>
                    <description><xsl:value-of select="@alt"/></description>
                    <owner><xsl:value-of select="@owner"/></owner>
                </resource>
            </content>
        </inlinefigure>
    </xsl:template>

    <!-- an image ("resource"), not being part of a paperfigure and not inside a paragraph-->
    <xsl:template match="img[@class='resource']" mode="editor" priority="2">
        <!-- only use original width, stored as attribute 'paperwidth'
        <xsl:variable name="width" select="number(@width) div $cm2px"/>
        -->
        <resource type="graphic">
            <name>
                <xsl:value-of select="reverse(tokenize(@src,'/'))[1]"/>
            </name>
            <id>
                <xsl:if test="@id">
                    <xsl:value-of select="@resource_id"/>
                </xsl:if>
            </id>
            <width>
                <xsl:if test="@paperwidth">
                    <xsl:value-of select="@paperwidth"/>
                </xsl:if>
            </width>
            <height>
                <xsl:if test="@paperheight">
                    <xsl:value-of select="@paperheight"/>
                </xsl:if>
            </height>
            <description>
                <xsl:value-of select="@alt"/>
            </description>
            <owner>
                <xsl:if test="@owner">
                    <xsl:value-of select="@owner"/>
                </xsl:if>
            </owner>
        </resource>
    </xsl:template>

    <!--
    <xsl:template match="div[@tag='objective']" mode="editor">
        <objective>
            <xsl:attribute name="id"><xsl:value-of select="div[@class='objective-id']"/></xsl:attribute>
            <xsl:value-of select="div[@class='objective-description']"/>
        </objective>
    </xsl:template>
    <xsl:template match="xhtml:div[@tag='objective']" mode="editor">
        <objective>
            <xsl:attribute name="id"><xsl:value-of select="xhtml:div[@class='objective-id']"/></xsl:attribute>
            <xsl:value-of select="xhtml:div[@class='objective-description']"/>
        </objective>
    </xsl:template>
    -->

</xsl:stylesheet>
