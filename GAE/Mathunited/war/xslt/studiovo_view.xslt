<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:xs="http://www.w3.org/2001/XMLSchema"
xmlns:exsl="http://exslt.org/common"
xmlns:mulom="http://www.mathunited.nl/nl-lom"
xmlns:saxon="http://saxon.sf.net/"
exclude-result-prefixes="saxon"
extension-element-prefixes="exsl">
<xsl:param name="item"/>
<xsl:param name="num"/>
<xsl:param name="refbase"/> <!-- used for includes: base path. Includes final / -->
<xsl:param name="ws_id"/>
<xsl:param name="comp"/>
<xsl:param name="option"/>
<xsl:param name="subcomp"/>
<xsl:param name="is_mobile"/>
<xsl:param name="id"/>
<xsl:param name="repo"/>
<xsl:param name="repo-path"/>
<xsl:param name="qtirepo"/>
<xsl:param name="baserepo-path"/>
<xsl:param name="requesturl"/>
<xsl:param name="component_id"/>
<xsl:param name="component_number"/>
<xsl:param name="component_file"/>
<xsl:param name="component_title"/>
<xsl:param name="component_subtitle"/>
<xsl:param name="subcomponent_number"/>
<xsl:param name="subcomponent_title"/>
<xsl:param name="subcomponent_index"/>
<xsl:param name="subcomponent_count"/>
    
<xsl:variable name="cm2px" select="number(50)"/>
<xsl:variable name="menu_color" select="subcomponent/meta/param[@name='menu-color']"/>
<xsl:variable name="background_color" select="subcomponent/meta/param[@name='background-color']"/>
<xsl:variable name="variant">studiovo_view</xsl:variable>
<xsl:variable name="intraLinkPrefix">
    <xsl:choose>
        <xsl:when test="$option">
            <xsl:value-of select="concat('view?comp=',$comp,'&amp;variant=',$variant,'&amp;option=',$option,'&amp;subcomp=')"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="concat('view?comp=',$comp,'&amp;variant=',$variant,'&amp;subcomp=')"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="cssfile">
    <xsl:choose>
      <xsl:when test="subcomponent/meta/param[@name='css-file']">
        <xsl:value-of select="subcomponent/meta/param[@name='css-file']"/>
      </xsl:when>	
      <xsl:otherwise>basis_studiovo.css?v=42</xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="overviewRef"><xsl:value-of select="string('/auteur/math4all.html')"/></xsl:variable>
<xsl:variable name="_cross_ref_as_links_" select="true()"/>
<xsl:variable name="_sheetref_as_links_" select="true()"/>
<xsl:variable name="lang">nl</xsl:variable>

<!--   /////////////////////////////////////////////   -->
<!--  Specific for GAE (do not copy from auteurssite): -->
<!--   /////////////////////////////////////////////   -->
<xsl:variable name="host_type">GAE</xsl:variable>
<xsl:variable name="docbase"></xsl:variable>
<xsl:variable name="urlbase"><xsl:value-of select="concat('http://mathunited.pragma-ade.nl:41080/data/',$refbase)"/></xsl:variable>
<xsl:variable name="urlprefix">/</xsl:variable>
<!--   /////////////////////////////////////////////   -->
<!--   /////////////////////////////////////////////   -->

<xsl:output method="html" doctype-system="http://www.w3.org/TR/html4/strict.dtd" doctype-public="-//W3C//DTD HTML 4.01//EN"
indent="yes" encoding="utf-8"/>

<xsl:include href="calstable.xslt"/>
<xsl:include href="content.xslt"/>
<xsl:include href="studiovo_exercises.xslt"/>

  <!--   **************** -->
<!--   START PROCESSING -->
<!--   **************** -->
<xsl:template match="/">
<html>
<head>
	<link type="text/css" href="{$urlprefix}javascript/jquery-ui-1.8.15.custom/css/ui-lightness/jquery-ui-1.8.15.custom.css" rel="Stylesheet" />
	<script type="text/javascript" src="{$urlprefix}javascript/jquery-ui-1.8.15.custom/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="{$urlprefix}javascript/jquery-ui-1.8.15.custom/js/jquery-ui-1.8.15.custom.min.js"></script>
	<script type="text/javascript" src="{$urlprefix}javascript/MathUnited.js"/>
	<script type="text/javascript" src="{$urlprefix}javascript/MathUnited_studiovo.js?v=13"/>
	<script type="text/javascript" src="{$urlprefix}javascript/jquery.ui.touch-punch.min.js"/>
	<script type="text/javascript" src="{$urlprefix}javascript/jquery.jplayer.min.js"/>
	<script type="text/javascript" src="{$urlprefix}javascript/jquery.scrollIntoView.min.js"/>
	<script type="text/javascript" src="{$urlprefix}javascript/jquery.ba-postmessage.js"/>
	<script type="text/javascript" src="{$urlprefix}javascript/readspeaker/ReadSpeaker.js?pids=embhl&amp;skin=ReadSpeakerMiniSkin"/>
    <link rel="stylesheet" href="{$urlprefix}css/content.css" type="text/css"/>
    <link rel="stylesheet" href="{$urlprefix}css/{$cssfile}" type="text/css" />
    
   <title><xsl:value-of select="$subcomponent_title"/></title>
   
   <link href="https://vjs.zencdn.net/c/video-js.css" rel="stylesheet"/>
   <script src="https://vjs.zencdn.net/c/video.js"></script>	

    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
            extensions: ["mml2jax.js","asciimath2jax.js"],
            config : ["MMLorHTML.js" ],
            AsciiMath: {
                decimal: ","
            },
            jax: ["input/MathML","input/AsciiMath"],
            "HTML-CSS": {
                scale: 90
            }
        });
    </script>
    <script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <script type="text/javascript">
    	var logintoken = "";
    	var userrole = "";
    	var schoolcode = "";
    	$.receiveMessage(
		  function(e){
		  	if (e.data.indexOf('readspeaker.com') == -1) // hack, origin filtering does not seem to work so message from readspeaker are received
		  	{
			    if (e.data.substring(0,7).toLowerCase() == 'http://' || e.data.substring(0,8).toLowerCase() == 'https://')
			    {
			    	toggleParentPopup(e.data);
			    }
				else 
				{
			    	logintoken = e.data;
			    	// TEST CODE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			    	if ('<xsl:value-of select="$requesturl"/>'.indexOf('&amp;role=student') > -1)
			    		userrole = "student";
			    	else if ('<xsl:value-of select="$requesturl"/>'.indexOf('&amp;role=employee') > -1)
			    		userrole = "employee";
			    	var idx = '<xsl:value-of select="$requesturl"/>'.indexOf('&amp;schoolcode=');
			    	if (idx > -1)
			    		schoolcode = '<xsl:value-of select="$requesturl"/>'.substring(idx + 12);
			    	// END OF TEST CODE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			    }
			 }
		  },
		  function (origin) {
		  	return 
		  		'<xsl:value-of select="$requesturl"/>'.toLowerCase().substring(0, origin.length) == origin.toLowerCase()
		  		||
	            origin.toLowerCase() == 'http://www.eindexamensite.nl'
          }
		);
    </script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-3488697-8', 'auto');
	  ga('send', 'pageview');
	</script>    
    <style>
        <xsl:if test="$background_color">
    		body { background-color: <xsl:value-of select="$background_color"/>; }
        </xsl:if>
        <xsl:if test="$menu_color">
			#menubar { background-color:<xsl:value-of select="$menu_color"/>; }
        </xsl:if>
        <xsl:if test="subcomponent/meta/param[@name='menu-text-color']">
			#menubar .menu-item { color:<xsl:value-of select="subcomponent/meta/param[@name='menu-text-color']"/>; }
        </xsl:if>
        <xsl:if test="subcomponent/meta/param[@name='title-text-color']">
        	#kruimelpad { color:<xsl:value-of select="subcomponent/meta/param[@name='title-text-color']"/>; }
        	.subcomponent-title { color:<xsl:value-of select="subcomponent/meta/param[@name='title-text-color']"/>; }
        </xsl:if>
        <xsl:if test="subcomponent/meta/param[@name='menu-link-color']">
			#menubar .menu-item.selected { color:<xsl:value-of select="subcomponent/meta/param[@name='menu-link-color']"/>; }
			#menubar .submenu-item.selected { color:<xsl:value-of select="subcomponent/meta/param[@name='menu-link-color']"/>; }
        </xsl:if>
    </style>
</head>

<!--   **************** -->
<!--        BODY        -->
<!--   **************** -->
<body>
<div class="pageDiv">
    <div id="menubar">
        <div id="logo">
		   <xsl:choose>
		      <xsl:when test="subcomponent/meta/param[@name='menu-image']">
			       <xsl:choose>
			          <xsl:when test="$host_type='GAE'">
			          	<img src="{subcomponent/meta/param[@name='menu-image']}" />
			          </xsl:when>
			          <xsl:otherwise>
			          	<img src="{concat($urlbase, subcomponent/meta/param[@name='menu-image']/resource/name)}" />
			          </xsl:otherwise>
			       </xsl:choose>
		      </xsl:when>
		      <xsl:otherwise>
            	<img src="{$urlprefix}sources_studiovo/logo.png"/>
	    	  </xsl:otherwise>
	   	  </xsl:choose>

            <span id="logo-text"><xsl:value-of select="$component_subtitle"/></span>
        </div>
        <xsl:apply-templates select="subcomponent/componentcontent/*" mode="navigation"/>

        <div id="menu-lines">
            <div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/><div class="menu-line"/>
        </div>
    </div>
    <div id="page-right">
        <div id="header">
            <xsl:attribute name="style">
               background-image: url(
		       <xsl:choose>
		          <xsl:when test="$host_type='GAE'">
		             <xsl:value-of select="subcomponent/meta/param[@name='banner-image']"/>
		          </xsl:when>
		          <xsl:otherwise>
		             <xsl:value-of select="concat($urlbase, subcomponent/meta/param[@name='banner-image']/resource/name)"/>
		          </xsl:otherwise>
		       </xsl:choose>)
			</xsl:attribute>
			<xsl:if test="subcomponent/meta/param[@name='show-login']='true'">
				<iframe class="login-frame" src="http://www.eindexamensite.nl/iframe-page.html?parentUrl={encode-for-uri($requesturl)}&amp;result=false"></iframe>
			</xsl:if>
        </div>
        <div id="ribbon">
            <span id="kruimelpad"></span>
		      <xsl:if test="subcomponent/meta/param[@name='read-speaker']">
		        	<script type="text/javascript">var readspeaker_lang="<xsl:value-of select="subcomponent/meta/param[@name='read-speaker']"/>"</script>
		            <div id="readspeaker_button1" class="rs_skip rsbtn_miniskin rs_preserve">
		                <a rel="nofollow" class="rsbtn_play" accesskey="L" title="Laat de tekst voorlezen met ReadSpeaker">
		                    <span class="rsbtn_left rsimg rspart"><span class="rsbtn_text"><span>Lees voor</span></span></span>
		                    <span class="rsbtn_right rsimg rsplay rspart"></span>
		                </a>
		            </div>
		      </xsl:if>
            <span class="subcomponent-title"><xsl:value-of select="$subcomponent_title"/></span>
        </div>
        <div id="content">
            <xsl:apply-templates select="subcomponent/componentcontent/*"/>
        </div>
    </div>
</div>
<div class="popup-content" id="parent-popup">
    <xsl:attribute name="title">
        <xsl:value-of select="@title"/>
    </xsl:attribute>
    <iframe width="800" height="620"></iframe>
</div>
</body>
</html>
</xsl:template>

<xsl:template match="mulom:*"/>
<xsl:template match="mulom:*" mode="content"/>
<!--   **************** -->
<!--    NAVIGATION   -->
<!--   **************** -->
<xsl:template match="fragment" mode="navigation">
    <xsl:variable name="pos" select="position()"/>
    <div class="menu-hierarchy">
        <div class="menu-item" onclick="javascript:SVO_triggerMenuItem(this)">
            <xsl:if test="not(@education='false') and (count(../fragment) &gt; 1)">
                <xsl:value-of select="('A','B','C','D','E','F','G','H','I','J','K','L','M','N')[$pos]"/>&#160;
            </xsl:if>
            <xsl:value-of select="title"/>
        </div>
        <xsl:apply-templates select="*" mode="navigation">
            <xsl:with-param name="menuref" select="concat('explore-',$pos)"></xsl:with-param>
        </xsl:apply-templates>
    </div>
</xsl:template>

<xsl:template match="link" mode="navigation">
	<div class="menu-hierarchy">
		<div class="menu-item">
			<a class="menu-link">
				<xsl:attribute name="href">
					<xsl:value-of select="url"/>
				</xsl:attribute>
				<xsl:if test="target">
					<xsl:attribute name="target">
						<xsl:value-of select="target"/>
					</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="title"/>
			</a>
		</div>
	</div>
</xsl:template>

<!-- explore, exercises can be remove -->
<xsl:template match="explore" mode="navigation">
    <xsl:variable name="pos" select="position()"/>
    <div class="menu-hierarchy">
        <div class="menu-item" onclick="javascript:SVO_triggerMenuItem(this)">
            <xsl:value-of select="('A','B','C','D','E')[$pos]"/>&#160;
            <xsl:value-of select="title"/>
        </div>
        <xsl:apply-templates select="*" mode="navigation">
            <xsl:with-param name="menuref" select="concat('explore-',$pos)"></xsl:with-param>
        </xsl:apply-templates>
    </div>
</xsl:template>
<xsl:template match="exercises" mode="navigation">
    <xsl:variable name="pos" select="position()"/>
    <div class="menu-hierarchy">
        <div class="menu-item" onclick="javascript:SVO_triggerMenuItem(this)">
            <xsl:value-of select="('A','B','C','D','E')[$pos]"/>&#160;
            <xsl:value-of select="title"/>
        </div>
        <xsl:apply-templates select="*" mode="navigation">
            <xsl:with-param name="menuref" select="concat('exercises-',$pos)"></xsl:with-param>
        </xsl:apply-templates>
    </div>
</xsl:template>
<xsl:template match="digest" mode="navigation">
    <xsl:variable name="pos" select="position()"/>
    <div class="menu-hierarchy">
        <div class="menu-item" onclick="javascript:SVO_triggerMenuItem(this)">
            <xsl:value-of select="('A','B','C','D','E')[$pos]"/>&#160;
            <xsl:value-of select="title"/>
        </div>
        <xsl:apply-templates select="*" mode="navigation">
            <xsl:with-param name="menuref" select="concat('digest-',$pos)"></xsl:with-param>
        </xsl:apply-templates>
    </div>
</xsl:template>

<xsl:template match="block | exercise" mode="navigation">
    <xsl:param name="menuref"/>
    <div class="submenu-item" id="{concat($menuref,'-',position())}"  
            tabid="{concat('tab-',$menuref,'-',position())}" onclick="javascript:SVO_triggerSubMenuItem(this)">
        <xsl:if test="count(../*) &lt; 3">
            <!-- if there is only one submenu, hide it in css but keep its functionality (so still render it) -->
            <xsl:attribute name="style">
                display:none;
            </xsl:attribute>
        </xsl:if>
        <xsl:value-of select="title"/>
    </div>
</xsl:template>

<xsl:template match="fragment/link" mode="navigation">
    <xsl:param name="menuref"/>
    <div class="submenu-item" id="{concat($menuref,'-',position())}"  
            tabid="{concat('tab-',$menuref,'-',position())}">
        <a class="menu-link">
			<xsl:attribute name="href">
				<xsl:value-of select="url"/>
			</xsl:attribute>
			<xsl:if test="target">
				<xsl:attribute name="target">
					<xsl:value-of select="target"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:value-of select="title"/>
        </a>
    </div>
</xsl:template>

<!--  ******************* -->
<!--   CONTENT STRUCTURE  -->
<!--  ******************* -->
<xsl:template match="fragment">
    <xsl:apply-templates select="*">
        <xsl:with-param name="menuref" select="concat('explore-',position())"/>
    </xsl:apply-templates>
</xsl:template>
<xsl:template match="explore">
    <xsl:apply-templates select="*">
        <xsl:with-param name="menuref" select="concat('explore-',position())"/>
    </xsl:apply-templates>
</xsl:template>
<xsl:template match="exercises">
    <xsl:apply-templates select="*">
        <xsl:with-param name="menuref" select="concat('exercises-',position())"/>
    </xsl:apply-templates>
</xsl:template>
<xsl:template match="digest">
    <xsl:apply-templates select="*">
        <xsl:with-param name="menuref" select="concat('digest-',position())"/>
    </xsl:apply-templates>
</xsl:template>
<xsl:template match="block">
    <xsl:param name="menuref"/>
    <div class="content-tab" id="{concat('tab-',$menuref,'-',position())}">
    	<xsl:if test="@noreadspeaker=1">
    		<xsl:attribute name="noreadspeaker">1</xsl:attribute>
    	</xsl:if>
        <xsl:apply-templates mode="content"/>
    </div>
</xsl:template>

<!--xsl:template match="block" mode="content">
    <xsl:apply-templates mode="content"/>
</xsl:template-->
<xsl:template match="block/title" mode="content"></xsl:template>
<xsl:template match="include" mode="content">
    <xsl:apply-templates select="document(concat($docbase,@filename))" mode="content"/>
</xsl:template>

<xsl:template match="p">
    <xsl:apply-templates mode="content"/>
</xsl:template>

<xsl:template match="result" mode="content">
	<iframe class="result-frame" ontab='$(this).attr("src", "/viewresult?repo={$repo}&amp;threadid={@layout}&amp;logintoken=" + encodeURIComponent(logintoken) + "&amp;userrole=" + userrole + "&amp;schoolcode=" + schoolcode)' src="/iframeloading.html"></iframe>
</xsl:template>

<xsl:template match="textref" mode="content">
    <xsl:choose>
        <xsl:when test="@ref">
            <span class="textref" ref="{@ref}"><xsl:value-of select="."/></span>
        </xsl:when>
        <xsl:otherwise>
            <xsl:variable name="_comp">
                <xsl:choose>
                    <xsl:when test="@comp"><xsl:value-of select="@comp"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$comp"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:variable name="_subcomp">
                <xsl:choose>
                    <xsl:when test="@subcomp"><xsl:value-of select="@subcomp"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$subcomp"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            
            <xsl:choose>
                <xsl:when test="$_cross_ref_as_links_">
                    <a class="textref" item="{@item}">
                        <xsl:if test="@target">
                            <xsl:attribute name="target"><xsl:value-of select="@target"/></xsl:attribute>
                        </xsl:if>
                        <xsl:attribute name="href"><xsl:value-of select="concat('view?comp=',$_comp,'&amp;subcomp=',$_subcomp,'&amp;variant=',$variant,'&amp;repo=',$repo)"/></xsl:attribute>
                        <xsl:value-of select="."/>

                    </a>
                </xsl:when>
                <xsl:otherwise>
                    <span class="textref" item="{@item}">
                        <xsl:value-of select="."/>
                    </span>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="pages" mode="content">
    <div class="pages-container">
        <xsl:apply-templates select="page" mode="content"/>
        <div class="page-navigator">
            <xsl:for-each select="page">
            	<xsl:variable name="noreadspeaker"><xsl:choose><xsl:when test="@noreadspeaker=1">true</xsl:when><xsl:otherwise>false</xsl:otherwise></xsl:choose></xsl:variable>
                <div num="{position()}" onclick="javascript:togglePage(this)" noreadspeaker="{$noreadspeaker}">
                	<xsl:attribute name="class">page-navigator-ref <xsl:if test="position() = 1">selected</xsl:if></xsl:attribute>
                	<xsl:value-of select="position()"/>
                </div>
            </xsl:for-each>
            <div style="clear:both"/>
        </div>
    </div>
</xsl:template>
<xsl:template match="page" mode="content">
    <xsl:variable name="pos" select="position()"/>
	<xsl:variable name="id"><xsl:value-of select="translate(document-uri(/), '/. ', '-')" />_<xsl:number level="any" /></xsl:variable>
    <div num="{$pos}" id="{$id}">
        <xsl:choose>
            <xsl:when test="$pos=1">
                <xsl:attribute name="class">page selected</xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
                <xsl:attribute name="class">page</xsl:attribute>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:apply-templates mode="content"/>
    </div>
</xsl:template>
<xsl:template match='block[@medium="web"]'><xsl:apply-templates/></xsl:template>

<!--  ******************** -->
<!--   EXERCISES (QTI)     -->
<!--  ******************** -->
<xsl:template match="assessment" mode="content">
    <div class="assessment-wrapper">
    	<xsl:variable name="src">
	        <xsl:choose>
	            <xsl:when test="@player='vo'"><xsl:value-of select="concat('http://qti-player.appspot.com/render.jsp?repo=',$qtirepo,'&amp;id=',@src)"/></xsl:when>
	            <xsl:otherwise><xsl:value-of select="concat('http://qt-studiovo.pulseon.nl/qt/player.html?testId=',@src,'&amp;lang=nl-NL&amp;window=false')"/></xsl:otherwise>
	        </xsl:choose>    
        </xsl:variable>
        
        <xsl:choose>
            <xsl:when test="@width"><xsl:attribute name="popup_width" select="@width"/></xsl:when>
            <xsl:otherwise><xsl:attribute name="popup_width" select="495"/></xsl:otherwise>
        </xsl:choose>    
        <xsl:choose>
            <xsl:when test="@display='popup'">
                <div class="assessment-button">
                    <span class="assessment-label" onclick="javascript:toggleAssessment(this, '{$src}&amp;logintoken=' + encodeURIComponent(logintoken))"><xsl:value-of select="@label"/></span>
                    <span class="assessment-label-text"><xsl:apply-templates mode="content"/></span>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <p><xsl:apply-templates mode="content"/></p>
            </xsl:otherwise>
        </xsl:choose>
        <div class="assessment-content">
        <iframe>
            <xsl:if test="not(@display='popup')">
                <xsl:attribute name="class">visible</xsl:attribute>
            </xsl:if>
            <xsl:attribute name="style">
                <xsl:choose>
                    <xsl:when test="@width">width:<xsl:value-of select="@width"/>px;</xsl:when>
                    <xsl:otherwise><xsl:attribute name="width">width:495px;</xsl:attribute></xsl:otherwise>
                </xsl:choose><xsl:choose>
                    <xsl:when test="@height">height:<xsl:value-of select="@height"/>px;</xsl:when>
                    <xsl:otherwise>height:300px;</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:if test="not(@display='popup')">
                <xsl:attribute name="src" select="$src"/>
            </xsl:if>
            <!--
            <xsl:attribute name="src" select="concat('http://qt-demo.pulseon.nl/qt/player.html?testId=0k2xWZR1aR33tx_9MP-_qXbIXncQzNnCAWsbUDdY8BQ','&amp;lang=nl-NL&amp;window=',$dowindow)"/>
            -->        
            <xsl:apply-templates mode="content"/>
        </iframe>
        </div>
    </div>
</xsl:template>

<!--  ******************** -->
<!--   EXERCISES (TVDD)    -->
<!--  ******************** -->
<xsl:template match="toets" mode="content">
    <div class="toets-wrapper">
    	<xsl:variable name="src">
	    	<xsl:value-of select="concat('http://toetsvandedag.studiopabo.nl/thematoets.jsp?frame=1&amp;group=',@groupid)"/>
        </xsl:variable>
        
        <xsl:choose>
            <xsl:when test="@width"><xsl:attribute name="popup_width" select="@width"/></xsl:when>
            <xsl:otherwise><xsl:attribute name="popup_width" select="800"/></xsl:otherwise>
        </xsl:choose>    
        <xsl:choose>
            <xsl:when test="@display='popup'">
                <div class="assessment-button">
                    <span class="assessment-label" onclick="javascript:toggleToets(this, '{$src}&amp;logintoken=' + encodeURIComponent(logintoken))"><xsl:value-of select="@label"/></span>
                    <span class="assessment-label-text"><xsl:apply-templates mode="content"/></span>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <p><xsl:apply-templates mode="content"/></p>
            </xsl:otherwise>
        </xsl:choose>
        <div class="toets-content">
        <iframe>
            <xsl:if test="not(@display='popup')">
                <xsl:attribute name="class">visible</xsl:attribute>
            </xsl:if>
            <xsl:attribute name="style">
                <xsl:choose>
                    <xsl:when test="@width">width:<xsl:value-of select="@width"/>px;</xsl:when>
                    <xsl:otherwise><xsl:attribute name="width">width:800px;</xsl:attribute></xsl:otherwise>
                </xsl:choose><xsl:choose>
                    <xsl:when test="@height">height:<xsl:value-of select="@height"/>px;</xsl:when>
                    <xsl:otherwise>height:620px;</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:if test="not(@display='popup')">
                <xsl:attribute name="src" select="$src"/>
            </xsl:if>
            <xsl:apply-templates mode="content"/>
        </iframe>
        </div>
    </div>
</xsl:template>

<!-- overrule default in content.xslt: images are in folder of xml content -->
<xsl:template match="resource" mode="content" priority="2">
   <xsl:variable name="width" select="number(substring-before(width,'cm'))*$cm2px"/>
   <img>
       <xsl:choose>
          <xsl:when test="$host_type='GAE'">
             <xsl:attribute name="src"><xsl:value-of select="name"/></xsl:attribute>
          </xsl:when>
          <xsl:otherwise>
             <xsl:attribute name="src"><xsl:value-of select="concat($urlbase,name)"/></xsl:attribute>
          </xsl:otherwise>
       </xsl:choose>
       <xsl:if test="$width>0">
           <xsl:attribute name="style">width:<xsl:value-of select="$width"/>px</xsl:attribute>
       </xsl:if>
   </img>
</xsl:template>
<!-- overrule default in content.xslt: resources are in folder of xml content -->
<xsl:template match="resourcelink" mode="content" priority="2">
    <a target="_blank" class="dox" > <!-- @class='dox' is used in GenerateQTI to find these resourcelink, do not change -->
        <xsl:for-each select="@*">
            <xsl:choose>
                <!-- relative url w.r.t. base path of content -->
                <xsl:when test="name()='href'">
	                <xsl:choose>
	                   <xsl:when test="$host_type='GAE'">
		                    <xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute>
	                   </xsl:when>
	                   <xsl:otherwise>
		                    <xsl:attribute name="href"><xsl:value-of select="concat($urlbase,.)"/></xsl:attribute>
	                   </xsl:otherwise>
	                </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                   <xsl:attribute name="{name()}">
                      <xsl:value-of select="."/>
                   </xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
        <xsl:apply-templates mode="content"/>
    </a>
</xsl:template>

<xsl:template match="popup" mode="content">
    <xsl:variable name="width">
        <xsl:choose>
            <xsl:when test="@width"><xsl:value-of select="@width"/></xsl:when>
            <xsl:otherwise>500</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:variable name="restart">
      <xsl:choose>
        <xsl:when test="@restart">
          <xsl:value-of select="@restart"/>
        </xsl:when>
        <xsl:otherwise>false</xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
  <div class="popup-wrapper">
       <span class="popup-label" onclick="{concat('javascript:togglePopup(',$width,',',$restart,', this)')}"><xsl:value-of select="@label"/></span>
       <span class="popup-label-text"><xsl:value-of select="@titel"/></span>
       <div class="popup-content">
           <xsl:attribute name="title">
               <xsl:value-of select="@titel"/>
           </xsl:attribute>
           <xsl:apply-templates mode="content"/>
       </div>
   </div>    
</xsl:template>

<xsl:template match="movie" mode="content" priority="2">
    <div class="movie-wrapper">
        <xsl:if test="@optional='true'">
            <img src="{$urlprefix}sources/movie_icon_60.gif" class="studiovo-movie-icon" onclick="javascript:toggleMovie(this)"/>
            <span class="movie-title"><xsl:value-of select="@title"/></span>
        </xsl:if>
        <div>
            <xsl:choose>
                <xsl:when test="@optional='true'">
                    <xsl:attribute name="class">movie optional</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="class">movie</xsl:attribute>                    
                </xsl:otherwise>
            </xsl:choose>
            <xsl:attribute name="style">width:<xsl:value-of select="@width"/>px;height:<xsl:value-of select="@height"/>px;</xsl:attribute>
            <xsl:choose>
                <xsl:when test="substring(@href,1,18) = 'http://www.youtube' or substring(@href,1,14) = 'http://youtube' or substring(@href,1,19) = 'https://www.youtube' or substring(@href,1,15) = 'https://youtube'">
                    <iframe frameborder="0" allowfullscreen="true">
                        <xsl:attribute name="height"><xsl:value-of select="@height"/></xsl:attribute>
                        <xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
                        <xsl:attribute name="src"><xsl:value-of select="@href"/></xsl:attribute>
                    </iframe>
                </xsl:when>
                <xsl:otherwise>
                    <video id="{generate-id()}" class="video-js vjs-default-skin" 
                            width="{@width}" height="{@height}"
                            controls="true" preload="none">
                        <xsl:choose>
							<xsl:when test="$host_type='GAE'">
								<source src="{@href}" type='video/mp4' />
								<xsl:if test="@href2">
									<source src="{@href2}" type='video/webm' />
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<source src="{concat($urlbase,@href)}" type='video/mp4' />
							</xsl:otherwise>
                        </xsl:choose>
                    </video>
                    <div style="clear:both"/>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </div>
</xsl:template>

<xsl:template match="audio" mode="content" priority="2">
    <xsl:choose>
        <xsl:when test="@inline='true'">
            <a onclick="this.getElementsByTagName('audio')[0].play()">
                <audio id="{generate-id()}" class="video-js vjs-default-skin"
                        width="{@width}" height="{@height}">
                    <xsl:choose>
                        <xsl:when test="$host_type='GAE'">
                            <source src="{@href}" type='audio/mp3'/>
                        </xsl:when>
                        <xsl:otherwise>
                            <source src="{concat($urlbase,@href)}" type='audio/mp3'/>
                        </xsl:otherwise>
                    </xsl:choose>
                </audio>
                <img src="{$urlprefix}sources_studiovo/speaker-16.png" class="studiovo-speaker-icon" />
            </a>
        </xsl:when>
        <xsl:otherwise>
            <div class="movie">
                <audio id="{generate-id()}" class="video-js vjs-default-skin"
                        width="{@width}" height="{@height}"
                        controls="true" preload="none">
                    <xsl:choose>
                        <xsl:when test="$host_type='GAE'">
                            <source src="{@href}" type='audio/mp3'/>
                        </xsl:when>
                        <xsl:otherwise>
                            <source src="{concat($urlbase,@href)}" type='audio/mp3'/>
                        </xsl:otherwise>
                    </xsl:choose>
                </audio>
            </div>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="iframe" mode="content" priority="2">
    <iframe>
        <xsl:copy-of select="@*[name()!='src']"/>
        <xsl:variable name="src">
            <xsl:choose>
              <xsl:when test="starts-with(@src,'http://') or starts-with(@src,'https://')">
                <xsl:value-of select="@src"/>
              </xsl:when>
              <xsl:when test="$host_type='GAE'">
                <xsl:value-of select="@src"/>
              </xsl:when>
              <xsl:otherwise>
                <xsl:value-of select="concat($urlbase, @src)"/>
              </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
      <!-- if the iframe is in a popup, do not show the content immediately (movies could be starting etc, also can improves performance) -->
      <xsl:choose>
        <xsl:when test="ancestor::popup">
          <xsl:attribute name="src-orig"><xsl:value-of select="$src"/></xsl:attribute>
        </xsl:when>
        <xsl:otherwise>
          <xsl:attribute name="src"><xsl:value-of select="$src"/></xsl:attribute>
        </xsl:otherwise>
      </xsl:choose>
    </iframe>
</xsl:template>

<xsl:template match="*"/>
<xsl:template match="*" mode="navigation"/>
</xsl:stylesheet>
