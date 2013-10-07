<?php
class EntityConverter {


    public static function convert_entities($string) {
    return preg_replace_callback('/&([a-zA-Z][a-zA-Z]*);/S', 
                                'EntityConverter::convert_entity', $string);
    }

    /* Swap HTML named entity with its numeric equivalent. If the entity
    * isn't in the lookup table, this function returns a blank, which
    * destroys the character in the output - this is probably the 
    * desired behaviour when producing XML. */
    public static function convert_entity($matches) {
    static $table = array(
'lt'=>'&#60;',
'gt'=>'&#62;',
'amp'=>'&#38;',
'nbsp'=>'&#160;',
'quot'=>'&#34;',
        //-------------------------------
        //insert entities below this line
        //-------------------------------
"AE"=>"&#x00C6;",
"AEacute"=>"&#x01FC;",
"AEligature"=>"&#x00C6;",
"Aacute"=>"&#x00C1;",
"Abreve"=>"&#x0102;",
"Abreveacute"=>"&#x1EAE;",
"Abrevedotbelow"=>"&#x1EB6;",
"Abrevegrave"=>"&#x1EB0;",
"Abrevehook"=>"&#x1EB2;",
"Abrevetilde"=>"&#x1EB4;",
"Acircumflex"=>"&#x00C2;",
"Acircumflexacute"=>"&#x1EA4;",
"Acircumflexdotbelow"=>"&#x1EAC;",
"Acircumflexgrave"=>"&#x1EA6;",
"Acircumflexhook"=>"&#x1EA8;",
"Acircumflextilde"=>"&#x1EAA;",
"Adiaeresis"=>"&#x00C4;",
"Adieresis"=>"&#x00C4;",
"Adotbelow"=>"&#x1EA0;",
"Agrave"=>"&#x00C0;",
"Ahook"=>"&#x1EA2;",
"Alpha"=>"&#x0391;",
"Alphatonos"=>"&#x0386;",
"Amacron"=>"&#x0100;",
"Aogonek"=>"&#x0104;",
"Aring"=>"&#x00C5;",
"Aringacute"=>"&#x01FA;",
"Atilde"=>"&#x00C3;",
"Beta"=>"&#x0392;",
"Cacute"=>"&#x0106;",
"Ccaron"=>"&#x010C;",
"Ccedilla"=>"&#x00C7;",
"Ccircumflex"=>"&#x0108;",
"Cdotaccent"=>"&#x010A;",
"Chi"=>"&#x03A7;",
"Dcaron"=>"&#x010E;",
"Dcroat"=>"&#x0110;",
"Delta"=>"&#x2206;",
"Dstroke"=>"&#x0110;",
"Eacute"=>"&#x00C9;",
"Ebreve"=>"&#x0114;",
"Ecaron"=>"&#x011A;",
"Ecircumflex"=>"&#x00CA;",
"Ecircumflexacute"=>"&#x1EBE;",
"Ecircumflexdotbelow"=>"&#x1EC6;",
"Ecircumflexgrave"=>"&#x1EC0;",
"Ecircumflexhook"=>"&#x1EC2;",
"Ecircumflextilde"=>"&#x1EC4;",
"Ediaeresis"=>"&#x00CB;",
"Edieresis"=>"&#x00CB;",
"Edotaccent"=>"&#x0116;",
"Edotbelow"=>"&#x1EB8;",
"Egrave"=>"&#x00C8;",
"Ehook"=>"&#x1EBA;",
"Emacron"=>"&#x0112;",
"Eng"=>"&#x014A;",
"Eogonek"=>"&#x0118;",
"Epsilon"=>"&#x0395;",
"Epsilontonos"=>"&#x0388;",
"Eta"=>"&#x0397;",
"Etatonos"=>"&#x0389;",
"Eth"=>"&#x00D0;",
"Etilde"=>"&#x1EBC;",
"Euro"=>"&#x20AC;",
"Gamma"=>"&#x0393;",
"Gbreve"=>"&#x011E;",
"Gcaron"=>"&#x01E6;",
"Gcircumflex"=>"&#x011C;",
"Gcommaaccent"=>"&#x0122;",
"Gdotaccent"=>"&#x0120;",
"Hbar"=>"&#x0126;",
"Hcircumflex"=>"&#x0124;",
"Hstroke"=>"&#x0126;",
"IJ"=>"&#x0132;",
"IJligature"=>"&#x0132;",
"Iacute"=>"&#x00CD;",
"Ibreve"=>"&#x012C;",
"Icircumflex"=>"&#x00CE;",
"Idiaeresis"=>"&#x00CF;",
"Idieresis"=>"&#x00CF;",
"Idotaccent"=>"&#x0130;",
"Idotbelow"=>"&#x1ECA;",
"Ifraktur"=>"&#x2111;",
"Igrave"=>"&#x00CC;",
"Ihook"=>"&#x1EC8;",
"Imacron"=>"&#x012A;",
"Iogonek"=>"&#x012E;",
"Iota"=>"&#x0399;",
"Iotadieresis"=>"&#x03AA;",
"Iotatonos"=>"&#x038A;",
"Itilde"=>"&#x0128;",
"Jcircumflex"=>"&#x0134;",
"Kappa"=>"&#x039A;",
"Kcommaaccent"=>"&#x0136;",
"Lacute"=>"&#x0139;",
"Lambda"=>"&#x039B;",
"Lcaron"=>"&#x013D;",
"Lcommaaccent"=>"&#x013B;",
"Ldot"=>"&#x013F;",
"Ldotmiddle"=>"&#x013F;",
"Lslash"=>"&#x0141;",
"Lstroke"=>"&#x0141;",
"Mu"=>"&#x039C;",
"Nacute"=>"&#x0143;",
"Ncaron"=>"&#x0147;",
"Ncommaaccent"=>"&#x0145;",
"Neng"=>"&#x014A;",
"Ntilde"=>"&#x00D1;",
"Nu"=>"&#x039D;",
"OE"=>"&#x0152;",
"OEligature"=>"&#x0152;",
"Oacute"=>"&#x00D3;",
"Obreve"=>"&#x014E;",
"Ocircumflex"=>"&#x00D4;",
"Ocircumflexacute"=>"&#x1ED0;",
"Ocircumflexdotbelow"=>"&#x1ED8;",
"Ocircumflexgrave"=>"&#x1ED2;",
"Ocircumflexhook"=>"&#x1ED4;",
"Ocircumflextilde"=>"&#x1ED6;",
"Odiaeresis"=>"&#x00D6;",
"Odieresis"=>"&#x00D6;",
"Odotbelow"=>"&#x1ECC;",
"Ograve"=>"&#x00D2;",
"Ohook"=>"&#x1ECE;",
"Ohorn"=>"&#x01A0;",
"Ohornacute"=>"&#x1EDA;",
"Ohorndotbelow"=>"&#x1EE2;",
"Ohorngrave"=>"&#x1EDC;",
"Ohornhook"=>"&#x1EDE;",
"Ohorntilde"=>"&#x1EE0;",
"Ohungarumlaut"=>"&#x0150;",
"Omacron"=>"&#x014C;",
"Omega"=>"&#x2126;",
"Omegatonos"=>"&#x038F;",
"Omicron"=>"&#x039F;",
"Omicrontonos"=>"&#x038C;",
"Oslash"=>"&#x00D8;",
"Oslashacute"=>"&#x01FE;",
"Ostroke"=>"&#x00D8;",
"Otilde"=>"&#x00D5;",
"Phi"=>"&#x03A6;",
"Pi"=>"&#x03A0;",
"Psi"=>"&#x03A8;",
"Racute"=>"&#x0154;",
"Rcaron"=>"&#x0158;",
"Rcommaaccent"=>"&#x0156;",
"Rfraktur"=>"&#x211C;",
"Rho"=>"&#x03A1;",
"Sacute"=>"&#x015A;",
"Scaron"=>"&#x0160;",
"Scedilla"=>"&#x015E;",
"Scircumflex"=>"&#x015C;",
"Scommaaccent"=>"&#x0218;",
"Sigma"=>"&#x03A3;",
"Tau"=>"&#x03A4;",
"Tbar"=>"&#x0166;",
"Tcaron"=>"&#x0164;",
"Tcedilla"=>"&#x0162;",
"Tcommaaccent"=>"&#x021A;",
"Theta"=>"&#x0398;",
"Thorn"=>"&#x00DE;",
"Tstroke"=>"&#x0166;",
"Uacute"=>"&#x00DA;",
"Ubreve"=>"&#x016C;",
"Ucircumflex"=>"&#x00DB;",
"Udiaeresis"=>"&#x00DC;",
"Udieresis"=>"&#x00DC;",
"Udotbelow"=>"&#x1EE4;",
"Ugrave"=>"&#x00D9;",
"Uhook"=>"&#x1EE6;",
"Uhorn"=>"&#x01AF;",
"Uhornacute"=>"&#x1EE8;",
"Uhorndotbelow"=>"&#x1EF0;",
"Uhorngrave"=>"&#x1EEA;",
"Uhornhook"=>"&#x1EEC;",
"Uhorntilde"=>"&#x1EEE;",
"Uhungarumlaut"=>"&#x00DB;",
"Umacron"=>"&#x016A;",
"Uogonek"=>"&#x0172;",
"Upsilon"=>"&#x03A5;",
"Upsilondieresis"=>"&#x03AB;",
"Upsilontonos"=>"&#x038E;",
"Uring"=>"&#x016E;",
"Utilde"=>"&#x0168;",
"Wacute"=>"&#x1E82;",
"Wcircumflex"=>"&#x0174;",
"Wdieresis"=>"&#x1E84;",
"Wgrave"=>"&#x1E80;",
"Xi"=>"&#x039E;",
"Yacute"=>"&#x00DD;",
"Ycircumflex"=>"&#x0176;",
"Ydiaeresis"=>"&#x0178;",
"Ydieresis"=>"&#x0178;",
"Ydotbelow"=>"&#x1EF4;",
"Ygrave"=>"&#x1EF2;",
"Yhook"=>"&#x1EF6;",
"Ytilde"=>"&#x1EF8;",
"Zacute"=>"&#x0179;",
"Zcaron"=>"&#x017D;",
"Zdotaccent"=>"&#x017B;",
"Zeta"=>"&#x0396;",
"aacute"=>"&#x00E1;",
"abreve"=>"&#x0103;",
"abreveacute"=>"&#x1EAF;",
"abrevedotbelow"=>"&#x1EB7;",
"abrevegrave"=>"&#x1EB1;",
"abrevehook"=>"&#x1EB3;",
"abrevetilde"=>"&#x1EB5;",
"acircumflex"=>"&#x00E2;",
"acircumflexacute"=>"&#x1EA5;",
"acircumflexdotbelow"=>"&#x1EAD;",
"acircumflexgrave"=>"&#x1EA7;",
"acircumflexhook"=>"&#x1EA9;",
"acircumflextilde"=>"&#x1EAB;",
"acute"=>"&#x00B4;",
"acutecomb"=>"&#x0301;",
"adiaeresis"=>"&#x00E4;",
"adieresis"=>"&#x00E4;",
"adotbelow"=>"&#x1EA1;",
"ae"=>"&#x00E6;",
"aeacute"=>"&#x01FD;",
"aeligature"=>"&#x00E6;",
"agrave"=>"&#x00E0;",
"ahook"=>"&#x1EA3;",
"aleph"=>"&#x2135;",
"alpha"=>"&#x03B1;",
"alphatonos"=>"&#x03AC;",
"amacron"=>"&#x0101;",
"ampersand"=>"&#x0026;",
"angle"=>"&#x2220;",
"angleleft"=>"&#x2329;",
"angleright"=>"&#x232A;",
"anoteleia"=>"&#x0387;",
"aogonek"=>"&#x0105;",
"approxequal"=>"&#x2248;",
"aring"=>"&#x00E5;",
"aringacute"=>"&#x01FB;",
"arrowboth"=>"&#x2194;",
"arrowdblboth"=>"&#x21D4;",
"arrowdbldown"=>"&#x21D3;",
"arrowdblleft"=>"&#x21D0;",
"arrowdblright"=>"&#x21D2;",
"arrowdblup"=>"&#x21D1;",
"arrowdown"=>"&#x2193;",
"arrowleft"=>"&#x2190;",
"arrowright"=>"&#x2192;",
"arrowup"=>"&#x2191;",
"arrowupdn"=>"&#x2195;",
"arrowupdnbse"=>"&#x21A8;",
"asciicircum"=>"&#x005E;",
"asciitilde"=>"&#x007E;",
"asterisk"=>"&#x002A;",
"asteriskmath"=>"&#x2217;",
"at"=>"&#x0040;",
"atilde"=>"&#x00E3;",
"backslash"=>"&#x005C;",
"bar"=>"&#x007C;",
"beta"=>"&#x03B2;",
"block"=>"&#x2588;",
"braceleft"=>"&#x007B;",
"braceright"=>"&#x007D;",
"bracketleft"=>"&#x005B;",
"bracketright"=>"&#x005D;",
"breve"=>"&#x02D8;",
"brokenbar"=>"&#x00A6;",
"bullet"=>"&#x2022;",
"cacute"=>"&#x0107;",
"caron"=>"&#x02C7;",
"carriagereturn"=>"&#x21B5;",
"ccaron"=>"&#x010D;",
"ccedilla"=>"&#x00E7;",
"ccircumflex"=>"&#x0109;",
"cdotaccent"=>"&#x010B;",
"cedilla"=>"&#x00B8;",
"cent"=>"&#x00A2;",
"chi"=>"&#x03C7;",
"circle"=>"&#x25CB;",
"circlemultiply"=>"&#x2297;",
"circleplus"=>"&#x2295;",
"circumflex"=>"&#x02C6;",
"club"=>"&#x2663;",
"colon"=>"&#x003A;",
"colonmonetary"=>"&#x20A1;",
"comma"=>"&#x002C;",
"congruent"=>"&#x2245;",
"copyright"=>"&#x00A9;",
"currency"=>"&#x00A4;",
"cyrillicA"=>"&#x0410;",
"cyrillicAE"=>"&#x04D4;",
"cyrillicAbreve"=>"&#x04D0;",
"cyrillicAdiaeresis"=>"&#x04D2;",
"cyrillicB"=>"&#x0411;",
"cyrillicBIGYUS"=>"&#x046A;",
"cyrillicBIGYUSiotified"=>"&#x046C;",
"cyrillicC"=>"&#x0426;",
"cyrillicCH"=>"&#x0427;",
"cyrillicCHEDC"=>"&#x04B6;",
"cyrillicCHEDCabkhasian"=>"&#x04BE;",
"cyrillicCHEabkhasian"=>"&#x04BC;",
"cyrillicCHEdiaeresis"=>"&#x04F4;",
"cyrillicCHEkhakassian"=>"&#x04CB;",
"cyrillicCHEvertstroke"=>"&#x04B8;",
"cyrillicD"=>"&#x0414;",
"cyrillicDASIAPNEUMATA"=>"&#x0485;",
"cyrillicDJE"=>"&#x0402;",
"cyrillicDZE"=>"&#x0405;",
"cyrillicDZEabkhasian"=>"&#x04E0;",
"cyrillicDZHE"=>"&#x040F;",
"cyrillicE"=>"&#x0415;",
"cyrillicELtail"=>"&#x04C5;",
"cyrillicEMtail"=>"&#x04CD;",
"cyrillicENDC"=>"&#x04A2;",
"cyrillicENGHE"=>"&#x04A4;",
"cyrillicENhook"=>"&#x04C7;",
"cyrillicENtail"=>"&#x04C9;",
"cyrillicEREV"=>"&#x042D;",
"cyrillicERY"=>"&#x042B;",
"cyrillicERtick"=>"&#x048E;",
"cyrillicEbreve"=>"&#x04D6;",
"cyrillicEdiaeresis"=>"&#x04EC;",
"cyrillicEgrave"=>"&#x0400;",
"cyrillicEiotified"=>"&#x0464;",
"cyrillicF"=>"&#x0424;",
"cyrillicFITA"=>"&#x0472;",
"cyrillicG"=>"&#x0413;",
"cyrillicGHEmidhook"=>"&#x0494;",
"cyrillicGHEstroke"=>"&#x0492;",
"cyrillicGHEupturn"=>"&#x0490;",
"cyrillicGJE"=>"&#x0403;",
"cyrillicH"=>"&#x0425;",
"cyrillicHA"=>"&#x04A8;",
"cyrillicHADC"=>"&#x04B2;",
"cyrillicHRDSN"=>"&#x042A;",
"cyrillicI"=>"&#x0418;",
"cyrillicIE"=>"&#x0404;",
"cyrillicII"=>"&#x0406;",
"cyrillicISHRT"=>"&#x0419;",
"cyrillicISHRTtail"=>"&#x048A;",
"cyrillicIZHITSA"=>"&#x0474;",
"cyrillicIZHITSAdoublegrave"=>"&#x0476;",
"cyrillicIdiaeresis"=>"&#x04E4;",
"cyrillicIgrave"=>"&#x040D;",
"cyrillicImacron"=>"&#x04E2;",
"cyrillicJE"=>"&#x0408;",
"cyrillicK"=>"&#x041A;",
"cyrillicKADC"=>"&#x049A;",
"cyrillicKAbashkir"=>"&#x04A0;",
"cyrillicKAhook"=>"&#x04C3;",
"cyrillicKAstroke"=>"&#x049E;",
"cyrillicKAvertstroke"=>"&#x049C;",
"cyrillicKJE"=>"&#x040C;",
"cyrillicKOPPA"=>"&#x0480;",
"cyrillicKSI"=>"&#x046E;",
"cyrillicL"=>"&#x041B;",
"cyrillicLITTLEYUS"=>"&#x0466;",
"cyrillicLITTLEYUSiotified"=>"&#x0468;",
"cyrillicLJE"=>"&#x0409;",
"cyrillicM"=>"&#x041C;",
"cyrillicN"=>"&#x041D;",
"cyrillicNJE"=>"&#x040A;",
"cyrillicO"=>"&#x041E;",
"cyrillicOMEGA"=>"&#x0460;",
"cyrillicOMEGAround"=>"&#x047A;",
"cyrillicOMEGAtitlo"=>"&#x047C;",
"cyrillicOT"=>"&#x047E;",
"cyrillicObarred"=>"&#x04E8;",
"cyrillicObarreddiaeresis"=>"&#x04EA;",
"cyrillicOdiaeresis"=>"&#x04E6;",
"cyrillicP"=>"&#x041F;",
"cyrillicPALATALIZATION"=>"&#x0484;",
"cyrillicPALOCHKA"=>"&#x04C0;",
"cyrillicPEmidhook"=>"&#x04A6;",
"cyrillicPSI"=>"&#x0470;",
"cyrillicPSILIPNEUMATA"=>"&#x0486;",
"cyrillicR"=>"&#x0420;",
"cyrillicS"=>"&#x0421;",
"cyrillicSCHWA"=>"&#x04D8;",
"cyrillicSCHWAdiaeresis"=>"&#x04DA;",
"cyrillicSDSC"=>"&#x04AA;",
"cyrillicSEMISOFT"=>"&#x048C;",
"cyrillicSFTSN"=>"&#x042C;",
"cyrillicSH"=>"&#x0428;",
"cyrillicSHCH"=>"&#x0429;",
"cyrillicSHHA"=>"&#x04BA;",
"cyrillicT"=>"&#x0422;",
"cyrillicTEDC"=>"&#x04AC;",
"cyrillicTETSE"=>"&#x04B4;",
"cyrillicTITLO"=>"&#x0483;",
"cyrillicTSHE"=>"&#x040B;",
"cyrillicU"=>"&#x0423;",
"cyrillicUK"=>"&#x0478;",
"cyrillicUSHRT"=>"&#x040E;",
"cyrillicUdiaeresis"=>"&#x04F0;",
"cyrillicUdoubleacute"=>"&#x04F2;",
"cyrillicUmacron"=>"&#x04EE;",
"cyrillicV"=>"&#x0412;",
"cyrillicYA"=>"&#x042F;",
"cyrillicYAT"=>"&#x0462;",
"cyrillicYERUdiaeresis"=>"&#x04F8;",
"cyrillicYI"=>"&#x0407;",
"cyrillicYO"=>"&#x0401;",
"cyrillicYU"=>"&#x042E;",
"cyrillicYstr"=>"&#x04AE;",
"cyrillicYstrstroke"=>"&#x04B0;",
"cyrillicZ"=>"&#x0417;",
"cyrillicZDSC"=>"&#x0498;",
"cyrillicZEdiaeresis"=>"&#x04DE;",
"cyrillicZH"=>"&#x0416;",
"cyrillicZHEbreve"=>"&#x04C1;",
"cyrillicZHEdescender"=>"&#x0496;",
"cyrillicZHEdiaeresis"=>"&#x04DC;",
"cyrillica"=>"&#x0430;",
"cyrillicabreve"=>"&#x04D1;",
"cyrillicadiaeresis"=>"&#x04D3;",
"cyrillicae"=>"&#x04D5;",
"cyrillicb"=>"&#x0431;",
"cyrillicbigyus"=>"&#x046B;",
"cyrillicbigyusiotified"=>"&#x046D;",
"cyrillicc"=>"&#x0446;",
"cyrillicch"=>"&#x0447;",
"cyrilliccheabkhasian"=>"&#x04BD;",
"cyrillicchedc"=>"&#x04B7;",
"cyrillicchedcabkhasian"=>"&#x04BF;",
"cyrillicchediaeresis"=>"&#x04F5;",
"cyrillicchekhakassian"=>"&#x04CC;",
"cyrillicchevertstroke"=>"&#x04B9;",
"cyrillicd"=>"&#x0434;",
"cyrillicdje"=>"&#x0452;",
"cyrillicdze"=>"&#x0455;",
"cyrillicdzeabkhasian"=>"&#x04E1;",
"cyrillicdzhe"=>"&#x045F;",
"cyrillice"=>"&#x0435;",
"cyrillicebreve"=>"&#x04D7;",
"cyrillicediaeresis"=>"&#x04ED;",
"cyrillicegrave"=>"&#x0450;",
"cyrilliceiotified"=>"&#x0465;",
"cyrilliceltail"=>"&#x04C6;",
"cyrillicemtail"=>"&#x04CE;",
"cyrillicendc"=>"&#x04A3;",
"cyrillicenghe"=>"&#x04A5;",
"cyrillicenhook"=>"&#x04C8;",
"cyrillicentail"=>"&#x04CA;",
"cyrillicerev"=>"&#x044D;",
"cyrillicertick"=>"&#x048F;",
"cyrillicery"=>"&#x044B;",
"cyrillicf"=>"&#x0444;",
"cyrillicfita"=>"&#x0473;",
"cyrillicg"=>"&#x0433;",
"cyrillicghemidhook"=>"&#x0495;",
"cyrillicghestroke"=>"&#x0493;",
"cyrillicgheupturn"=>"&#x0491;",
"cyrillicgje"=>"&#x0453;",
"cyrillich"=>"&#x0445;",
"cyrillicha"=>"&#x04A9;",
"cyrillichadc"=>"&#x04B3;",
"cyrillichrdsn"=>"&#x044A;",
"cyrillici"=>"&#x0438;",
"cyrillicidiaeresis"=>"&#x04E5;",
"cyrillicie"=>"&#x0454;",
"cyrillicigrave"=>"&#x045D;",
"cyrillicii"=>"&#x0456;",
"cyrillicimacron"=>"&#x04E3;",
"cyrillicishrt"=>"&#x0439;",
"cyrillicishrttail"=>"&#x048B;",
"cyrillicizhitsa"=>"&#x0475;",
"cyrillicizhitsadoublegrave"=>"&#x0477;",
"cyrillicje"=>"&#x0458;",
"cyrillick"=>"&#x043A;",
"cyrillickabashkir"=>"&#x04A1;",
"cyrillickadc"=>"&#x049B;",
"cyrillickahook"=>"&#x04C4;",
"cyrillickastroke"=>"&#x049F;",
"cyrillickavertstroke"=>"&#x049D;",
"cyrillickje"=>"&#x045C;",
"cyrillickoppa"=>"&#x0481;",
"cyrillicksi"=>"&#x046F;",
"cyrillicl"=>"&#x043B;",
"cyrilliclittleyus"=>"&#x0467;",
"cyrilliclittleyusiotified"=>"&#x0469;",
"cyrilliclje"=>"&#x0459;",
"cyrillicm"=>"&#x043C;",
"cyrillicn"=>"&#x043D;",
"cyrillicnje"=>"&#x045A;",
"cyrillico"=>"&#x043E;",
"cyrillicobarred"=>"&#x04E9;",
"cyrillicobarreddiaeresis"=>"&#x04EB;",
"cyrillicodiaeresis"=>"&#x04E7;",
"cyrillicomega"=>"&#x0461;",
"cyrillicomegaround"=>"&#x047B;",
"cyrillicomegatitlo"=>"&#x047D;",
"cyrillicot"=>"&#x047F;",
"cyrillicp"=>"&#x043F;",
"cyrillicpemidhook"=>"&#x04A7;",
"cyrillicpsi"=>"&#x0471;",
"cyrillicr"=>"&#x0440;",
"cyrillics"=>"&#x0441;",
"cyrillicschwa"=>"&#x04D9;",
"cyrillicschwadiaeresis"=>"&#x04DB;",
"cyrillicsdsc"=>"&#x04AB;",
"cyrillicsemisoft"=>"&#x048D;",
"cyrillicsftsn"=>"&#x044C;",
"cyrillicsh"=>"&#x0448;",
"cyrillicshch"=>"&#x0449;",
"cyrillicshha"=>"&#x04BB;",
"cyrillict"=>"&#x0442;",
"cyrillictedc"=>"&#x04AD;",
"cyrillictetse"=>"&#x04B5;",
"cyrillictshe"=>"&#x045B;",
"cyrillicu"=>"&#x0443;",
"cyrillicudiaeresis"=>"&#x04F1;",
"cyrillicudoubleacute"=>"&#x04F3;",
"cyrillicuk"=>"&#x0479;",
"cyrillicumacron"=>"&#x04EF;",
"cyrillicushrt"=>"&#x045E;",
"cyrillicv"=>"&#x0432;",
"cyrillicya"=>"&#x044F;",
"cyrillicyat"=>"&#x0463;",
"cyrillicyerudiaeresis"=>"&#x04F9;",
"cyrillicyi"=>"&#x0457;",
"cyrillicyo"=>"&#x0451;",
"cyrillicystr"=>"&#x04AF;",
"cyrillicystrstroke"=>"&#x04B1;",
"cyrillicyu"=>"&#x044E;",
"cyrillicz"=>"&#x0437;",
"cyrilliczdsc"=>"&#x0499;",
"cyrilliczediaeresis"=>"&#x04DF;",
"cyrilliczh"=>"&#x0436;",
"cyrilliczhebreve"=>"&#x04C2;",
"cyrilliczhedescender"=>"&#x0497;",
"cyrilliczhediaeresis"=>"&#x04DD;",
"dagger"=>"&#x2020;",
"Dagger"=>"&#8225;",
"daggerdbl"=>"&#x2021;",
"dcaron"=>"&#x010F;",
"dcroat"=>"&#x0111;",
"degree"=>"&#x00B0;",
"deg"=>"&#x00B0;",
"delta"=>"&#x03B4;",
"diamond"=>"&#x2666;",
"dieresis"=>"&#x00A8;",
"dieresistonos"=>"&#x0385;",
"divide"=>"&#x00F7;",
"dkshade"=>"&#x2593;",
"dnblock"=>"&#x2584;",
"dollar"=>"&#x0024;",
"dong"=>"&#x20AB;",
"dotaccent"=>"&#x02D9;",
"dotbelowcomb"=>"&#x0323;",
"dotlessi"=>"&#x0131;",
"dotmath"=>"&#x22C5;",
"dstroke"=>"&#x0111;",
"eacute"=>"&#x00E9;",
"ebreve"=>"&#x0115;",
"ecaron"=>"&#x011B;",
"ecircumflex"=>"&#x00EA;",
"ecircumflexacute"=>"&#x1EBF;",
"ecircumflexdotbelow"=>"&#x1EC7;",
"ecircumflexgrave"=>"&#x1EC1;",
"ecircumflexhook"=>"&#x1EC3;",
"ecircumflextilde"=>"&#x1EC5;",
"ediaeresis"=>"&#x00EB;",
"edieresis"=>"&#x00EB;",
"edotaccent"=>"&#x0117;",
"edotbelow"=>"&#x1EB9;",
"egrave"=>"&#x00E8;",
"ehook"=>"&#x1EBB;",
"eight"=>"&#x0038;",
"element"=>"&#x2208;",
"ellipsis"=>"&#x2026;",
"emacron"=>"&#x0113;",
"emdash"=>"&#x2014;",
"emptyset"=>"&#x2205;",
"endash"=>"&#x2013;",
"eng"=>"&#x014B;",
"eogonek"=>"&#x0119;",
"epsilon"=>"&#x03B5;",
"epsilontonos"=>"&#x03AD;",
"equal"=>"&#x003D;",
"equivalence"=>"&#x2261;",
"estimated"=>"&#x212E;",
"eta"=>"&#x03B7;",
"etatonos"=>"&#x03AE;",
"eth"=>"&#x00F0;",
"etilde"=>"&#x1EBD;",
"exclam"=>"&#x0021;",
"exclamdbl"=>"&#x203C;",
"exclamdown"=>"&#x00A1;",
"existential"=>"&#x2203;",
"female"=>"&#x2640;",
"figuredash"=>"&#x2012;",
"filledbox"=>"&#x25A0;",
"filledrect"=>"&#x25AC;",
"five"=>"&#x0035;",
"fiveeighths"=>"&#x215D;",
"florin"=>"&#x0192;",
"four"=>"&#x0034;",
"fraction"=>"&#x2044;",
"franc"=>"&#x20A3;",
"gamma"=>"&#x03B3;",
"gbreve"=>"&#x011F;",
"gcaron"=>"&#x01E7;",
"gcircumflex"=>"&#x011D;",
"gcommaaccent"=>"&#x0123;",
"gdotaccent"=>"&#x0121;",
"germandbls"=>"&#x00DF;",
"gradient"=>"&#x2207;",
"grave"=>"&#x0060;",
"gravecomb"=>"&#x0300;",
"greater"=>"&#x003E;",
"greaterequal"=>"&#x2265;",
"greekAlpha"=>"&#x0391;",
"greekAlphadasia"=>"&#x1F09;",
"greekAlphadasiaperispomeni"=>"&#x1F0F;",
"greekAlphadasiatonos"=>"&#x1F0D;",
"greekAlphadasiavaria"=>"&#x1F0B;",
"greekAlphaiotasub"=>"&#x1FBC;",
"greekAlphaiotasubdasia"=>"&#x1F89;",
"greekAlphaiotasubdasiaperispomeni"=>"&#x1F8F;",
"greekAlphaiotasubdasiatonos"=>"&#x1F8D;",
"greekAlphaiotasubdasiavaria"=>"&#x1F8B;",
"greekAlphaiotasubpsili"=>"&#x1F88;",
"greekAlphaiotasubpsiliperispomeni"=>"&#x1F8E;",
"greekAlphaiotasubpsilitonos"=>"&#x1F8C;",
"greekAlphaiotasubpsilivaria"=>"&#x1F8A;",
"greekAlphamacron"=>"&#x1FB9;",
"greekAlphapsili"=>"&#x1F08;",
"greekAlphapsiliperispomeni"=>"&#x1F0E;",
"greekAlphapsilitonos"=>"&#x1F0C;",
"greekAlphapsilivaria"=>"&#x1F0A;",
"greekAlphatonos"=>"&#x0386;",
"greekAlphavaria"=>"&#x1FBA;",
"greekAlphavrachy"=>"&#x1FB8;",
"greekBeta"=>"&#x0392;",
"greekChi"=>"&#x03A7;",
"greekCoronis"=>"&#x1FBD;",
"greekDelta"=>"&#x0394;",
"greekEpsilon"=>"&#x0395;",
"greekEpsilondasia"=>"&#x1F19;",
"greekEpsilondasiatonos"=>"&#x1F1D;",
"greekEpsilondasiavaria"=>"&#x1F1B;",
"greekEpsilonpsili"=>"&#x1F18;",
"greekEpsilonpsilitonos"=>"&#x1F1C;",
"greekEpsilonpsilivaria"=>"&#x1F1A;",
"greekEpsilontonos"=>"&#x0388;",
"greekEpsilonvaria"=>"&#x1FC8;",
"greekEta"=>"&#x0397;",
"greekEtadasia"=>"&#x1F29;",
"greekEtadasiaperispomeni"=>"&#x1F2F;",
"greekEtadasiatonos"=>"&#x1F2D;",
"greekEtadasiavaria"=>"&#x1F2B;",
"greekEtaiotasub"=>"&#x1FCC;",
"greekEtaiotasubdasia"=>"&#x1F99;",
"greekEtaiotasubdasiaperispomeni"=>"&#x1F9F;",
"greekEtaiotasubdasiatonos"=>"&#x1F9D;",
"greekEtaiotasubdasiavaria"=>"&#x1F9B;",
"greekEtaiotasubpsili"=>"&#x1F98;",
"greekEtaiotasubpsiliperispomeni"=>"&#x1F9E;",
"greekEtaiotasubpsilitonos"=>"&#x1F9C;",
"greekEtaiotasubpsilivaria"=>"&#x1F9A;",
"greekEtapsili"=>"&#x1F28;",
"greekEtapsiliperispomeni"=>"&#x1F2E;",
"greekEtapsilitonos"=>"&#x1F2C;",
"greekEtapsilivaria"=>"&#x1F2A;",
"greekEtatonos"=>"&#x0389;",
"greekEtavaria"=>"&#x1FCA;",
"greekGamma"=>"&#x0393;",
"greekIota"=>"&#x0399;",
"greekIotadasia"=>"&#x1F39;",
"greekIotadasiaperispomeni"=>"&#x1F3F;",
"greekIotadasiatonos"=>"&#x1F3D;",
"greekIotadasiavaria"=>"&#x1F3B;",
"greekIotadialytika"=>"&#x03AA;",
"greekIotamacron"=>"&#x1FD9;",
"greekIotapsili"=>"&#x1F38;",
"greekIotapsiliperispomeni"=>"&#x1F3E;",
"greekIotapsilitonos"=>"&#x1F3C;",
"greekIotapsilivaria"=>"&#x1F3A;",
"greekIotatonos"=>"&#x038A;",
"greekIotavaria"=>"&#x1FDA;",
"greekIotavrachy"=>"&#x1FD8;",
"greekKappa"=>"&#x039A;",
"greekLambda"=>"&#x039B;",
"greekMu"=>"&#x039C;",
"greekNu"=>"&#x039D;",
"greekOmega"=>"&#x03A9;",
"greekOmegadasia"=>"&#x1F69;",
"greekOmegadasiaperispomeni"=>"&#x1F6F;",
"greekOmegadasiatonos"=>"&#x1F6D;",
"greekOmegadasiavaria"=>"&#x1F6B;",
"greekOmegaiotasub"=>"&#x1FFC;",
"greekOmegaiotasubdasia"=>"&#x1FA9;",
"greekOmegaiotasubdasiaperispomeni"=>"&#x1FAF;",
"greekOmegaiotasubdasiatonos"=>"&#x1FAD;",
"greekOmegaiotasubdasiavaria"=>"&#x1FAB;",
"greekOmegaiotasubpsili"=>"&#x1FA8;",
"greekOmegaiotasubpsiliperispomeni"=>"&#x1FAE;",
"greekOmegaiotasubpsilitonos"=>"&#x1FAC;",
"greekOmegaiotasubpsilivaria"=>"&#x1FAA;",
"greekOmegapsili"=>"&#x1F68;",
"greekOmegapsiliperispomeni"=>"&#x1F6E;",
"greekOmegapsilitonos"=>"&#x1F6C;",
"greekOmegapsilivaria"=>"&#x1F6A;",
"greekOmegatonos"=>"&#x038F;",
"greekOmegavaria"=>"&#x1FFA;",
"greekOmicron"=>"&#x039F;",
"greekOmicrondasia"=>"&#x1F49;",
"greekOmicrondasiatonos"=>"&#x1F4D;",
"greekOmicrondasiavaria"=>"&#x1F4B;",
"greekOmicronpsili"=>"&#x1F48;",
"greekOmicronpsilitonos"=>"&#x1F4C;",
"greekOmicronpsilivaria"=>"&#x1F4A;",
"greekOmicrontonos"=>"&#x038C;",
"greekOmicronvaria"=>"&#x1FF8;",
"greekPhi"=>"&#x03A6;",
"greekPi"=>"&#x03A0;",
"greekPsi"=>"&#x03A8;",
"greekRho"=>"&#x03A1;",
"greekRhodasia"=>"&#x1FEC;",
"greekSigma"=>"&#x03A3;",
"greekSigmalunate"=>"&#x03F9;",
"greekTau"=>"&#x03A4;",
"greekTheta"=>"&#x0398;",
"greekUpsilon"=>"&#x03A5;",
"greekUpsilondasia"=>"&#x1F59;",
"greekUpsilondasiaperispomeni"=>"&#x1F5F;",
"greekUpsilondasiatonos"=>"&#x1F5D;",
"greekUpsilondasiavaria"=>"&#x1F5B;",
"greekUpsilondialytika"=>"&#x03AB;",
"greekUpsilonmacron"=>"&#x1FE9;",
"greekUpsilontonos"=>"&#x038E;",
"greekUpsilonvaria"=>"&#x1FEA;",
"greekUpsilonvrachy"=>"&#x1FE8;",
"greekXi"=>"&#x039E;",
"greekZeta"=>"&#x0396;",
"greekalpha"=>"&#x03B1;",
"greekalphadasia"=>"&#x1F01;",
"greekalphadasiaperispomeni"=>"&#x1F07;",
"greekalphadasiatonos"=>"&#x1F05;",
"greekalphadasiavaria"=>"&#x1F03;",
"greekalphaiotasub"=>"&#x1FB3;",
"greekalphaiotasubdasia"=>"&#x1F81;",
"greekalphaiotasubdasiaperispomeni"=>"&#x1F87;",
"greekalphaiotasubdasiatonos"=>"&#x1F85;",
"greekalphaiotasubdasiavaria"=>"&#x1F83;",
"greekalphaiotasubperispomeni"=>"&#x1FB7;",
"greekalphaiotasubpsili"=>"&#x1F80;",
"greekalphaiotasubpsiliperispomeni"=>"&#x1F86;",
"greekalphaiotasubpsilitonos"=>"&#x1F84;",
"greekalphaiotasubpsilivaria"=>"&#x1F82;",
"greekalphaiotasubtonos"=>"&#x1FB4;",
"greekalphaiotasubvaria"=>"&#x1FB2;",
"greekalphamacron"=>"&#x1FB1;",
"greekalphaoxia"=>"&#x1F71;",
"greekalphaperispomeni"=>"&#x1FB6;",
"greekalphapsili"=>"&#x1F00;",
"greekalphapsiliperispomeni"=>"&#x1F06;",
"greekalphapsilitonos"=>"&#x1F04;",
"greekalphapsilivaria"=>"&#x1F02;",
"greekalphatonos"=>"&#x03AC;",
"greekalphavaria"=>"&#x1F70;",
"greekalphavrachy"=>"&#x1FB0;",
"greekbeta"=>"&#x03B2;",
"greekchi"=>"&#x03C7;",
"greekdasia"=>"&#x1FFE;",
"greekdasiaperispomeni"=>"&#x1FDF;",
"greekdasiatonos"=>"&#x1FDE;",
"greekdasiavaria"=>"&#x1FDD;",
"greekdelta"=>"&#x03B4;",
"greekdialytikaperispomeni"=>"&#x1FC1;",
"greekdialytikatonos"=>"&#x1FEE;",
"greekdialytikavaria"=>"&#x1FED;",
"greekdigamma"=>"&#x03DD;",
"greekepsilon"=>"&#x03B5;",
"greekepsilonalt"=>"&#x03F5;",
"greekepsilondasia"=>"&#x1F11;",
"greekepsilondasiatonos"=>"&#x1F15;",
"greekepsilondasiavaria"=>"&#x1F13;",
"greekepsilonoxia"=>"&#x1F73;",
"greekepsilonpsili"=>"&#x1F10;",
"greekepsilonpsilitonos"=>"&#x1F14;",
"greekepsilonpsilivaria"=>"&#x1F12;",
"greekepsilontonos"=>"&#x03AD;",
"greekepsilonvaria"=>"&#x1F72;",
"greeketa"=>"&#x03B7;",
"greeketadasia"=>"&#x1F21;",
"greeketadasiaperispomeni"=>"&#x1F27;",
"greeketadasiatonos"=>"&#x1F25;",
"greeketadasiavaria"=>"&#x1F23;",
"greeketaiotasub"=>"&#x1FC3;",
"greeketaiotasubdasia"=>"&#x1F91;",
"greeketaiotasubdasiaperispomeni"=>"&#x1F97;",
"greeketaiotasubdasiatonos"=>"&#x1F95;",
"greeketaiotasubdasiavaria"=>"&#x1F93;",
"greeketaiotasubperispomeni"=>"&#x1FC7;",
"greeketaiotasubpsili"=>"&#x1F90;",
"greeketaiotasubpsiliperispomeni"=>"&#x1F96;",
"greeketaiotasubpsilitonos"=>"&#x1F94;",
"greeketaiotasubpsilivaria"=>"&#x1F92;",
"greeketaiotasubtonos"=>"&#x1FC4;",
"greeketaiotasubvaria"=>"&#x1FC2;",
"greeketaoxia"=>"&#x1F75;",
"greeketaperispomeni"=>"&#x1FC6;",
"greeketapsili"=>"&#x1F20;",
"greeketapsiliperispomeni"=>"&#x1F26;",
"greeketapsilitonos"=>"&#x1F24;",
"greeketapsilivaria"=>"&#x1F22;",
"greeketatonos"=>"&#x03AE;",
"greeketavaria"=>"&#x1F74;",
"greekfinalsigma"=>"&#x03C2;",
"greekgamma"=>"&#x03B3;",
"greekiota"=>"&#x03B9;",
"greekiotadasia"=>"&#x1F31;",
"greekiotadasiaperispomeni"=>"&#x1F37;",
"greekiotadasiatonos"=>"&#x1F35;",
"greekiotadasiavaria"=>"&#x1F33;",
"greekiotadialytika"=>"&#x03CA;",
"greekiotadialytikaperispomeni"=>"&#x1FD7;",
"greekiotadialytikatonos"=>"&#x0390;",
"greekiotadialytikavaria"=>"&#x1FD2;",
"greekiotamacron"=>"&#x1FD1;",
"greekiotaoxia"=>"&#x1F77;",
"greekiotaperispomeni"=>"&#x1FD6;",
"greekiotapsili"=>"&#x1F30;",
"greekiotapsiliperispomeni"=>"&#x1F36;",
"greekiotapsilitonos"=>"&#x1F34;",
"greekiotapsilivaria"=>"&#x1F32;",
"greekiotatonos"=>"&#x03AF;",
"greekiotavaria"=>"&#x1F76;",
"greekiotavrachy"=>"&#x1FD0;",
"greekkappa"=>"&#x03BA;",
"greekkoppa"=>"&#x03D9;",
"greeklambda"=>"&#x03BB;",
"greekmu"=>"&#x03BC;",
"greeknu"=>"&#x03BD;",
"greeknumkoppa"=>"&#x03DF;",
"greekomega"=>"&#x03C9;",
"greekomegadasia"=>"&#x1F61;",
"greekomegadasiaperispomeni"=>"&#x1F67;",
"greekomegadasiatonos"=>"&#x1F65;",
"greekomegadasiavaria"=>"&#x1F63;",
"greekomegaiotasub"=>"&#x1FF3;",
"greekomegaiotasubdasia"=>"&#x1FA1;",
"greekomegaiotasubdasiaperispomeni"=>"&#x1FA7;",
"greekomegaiotasubdasiatonos"=>"&#x1FA5;",
"greekomegaiotasubdasiavaria"=>"&#x1FA3;",
"greekomegaiotasubperispomeni"=>"&#x1FF7;",
"greekomegaiotasubpsili"=>"&#x1FA0;",
"greekomegaiotasubpsiliperispomeni"=>"&#x1FA6;",
"greekomegaiotasubpsilitonos"=>"&#x1FA4;",
"greekomegaiotasubpsilivaria"=>"&#x1FA2;",
"greekomegaiotasubtonos"=>"&#x1FF4;",
"greekomegaiotasubvaria"=>"&#x1FF2;",
"greekomegaoxia"=>"&#x1F7D;",
"greekomegaperispomeni"=>"&#x1FF6;",
"greekomegapsili"=>"&#x1F60;",
"greekomegapsiliperispomeni"=>"&#x1F66;",
"greekomegapsilitonos"=>"&#x1F64;",
"greekomegapsilivaria"=>"&#x1F62;",
"greekomegatonos"=>"&#x03CE;",
"greekomegavaria"=>"&#x1F7C;",
"greekomicron"=>"&#x03BF;",
"greekomicrondasia"=>"&#x1F41;",
"greekomicrondasiatonos"=>"&#x1F45;",
"greekomicrondasiavaria"=>"&#x1F43;",
"greekomicronoxia"=>"&#x1F79;",
"greekomicronpsili"=>"&#x1F40;",
"greekomicronpsilitonos"=>"&#x1F44;",
"greekomicronpsilivaria"=>"&#x1F42;",
"greekomicrontonos"=>"&#x03CC;",
"greekomicronvaria"=>"&#x1F78;",
"greekoxia"=>"&#x1FFD;",
"greekperispomeni"=>"&#x1FC0;",
"greekphi"=>"&#x03C6;",
"greekphialt"=>"&#x03D5;",
"greekpi"=>"&#x03C0;",
"greekpialt"=>"&#x03D6;",
"greekprosgegrammeni"=>"&#x1FBE;",
"greekpsi"=>"&#x03C8;",
"greekpsili"=>"&#x1FBF;",
"greekpsiliperispomeni"=>"&#x1FCF;",
"greekpsilitonos"=>"&#x1FCE;",
"greekpsilivaria"=>"&#x1FCD;",
"greekrho"=>"&#x03C1;",
"greekrhoalt"=>"&#x03F1;",
"greekrhodasia"=>"&#x1FE5;",
"greekrhopsili"=>"&#x1FE4;",
"greeksampi"=>"&#x03E1;",
"greeksigma"=>"&#x03C3;",
"greeksigmalunate"=>"&#x03F2;",
"greekstigma"=>"&#x03DB;",
"greektau"=>"&#x03C4;",
"greektheta"=>"&#x03B8;",
"greekthetaalt"=>"&#x03D1;",
"greekupsilon"=>"&#x03C5;",
"greekupsilondasia"=>"&#x1F51;",
"greekupsilondasiaperispomeni"=>"&#x1F57;",
"greekupsilondasiatonos"=>"&#x1F55;",
"greekupsilondasiavaria"=>"&#x1F53;",
"greekupsilondiaeresis"=>"&#x03CB;",
"greekupsilondialytika"=>"&#x03CB;",
"greekupsilondialytikaperispomeni"=>"&#x1FE7;",
"greekupsilondialytikatonos"=>"&#x03B0;",
"greekupsilondialytikavaria"=>"&#x1FE2;",
"greekupsilonmacron"=>"&#x1FE1;",
"greekupsilonoxia"=>"&#x1F7B;",
"greekupsilonperispomeni"=>"&#x1FE6;",
"greekupsilonpsili"=>"&#x1F50;",
"greekupsilonpsiliperispomeni"=>"&#x1F56;",
"greekupsilonpsilitonos"=>"&#x1F54;",
"greekupsilonpsilivaria"=>"&#x1F52;",
"greekupsilontonos"=>"&#x03CD;",
"greekupsilonvaria"=>"&#x1F7A;",
"greekupsilonvrachy"=>"&#x1FE0;",
"greekvaria"=>"&#x1FEF;",
"greekxi"=>"&#x03BE;",
"greekzeta"=>"&#x03B6;",
"guillemotleft"=>"&#x00AB;",
"guillemotright"=>"&#x00BB;",
"guilsingleleft"=>"&#x2039;",
"guilsingleright"=>"&#x203A;",
"guilsinglleft"=>"&#x2039;",
"guilsinglright"=>"&#x203A;",
"hbar"=>"&#x0127;",
"hcircumflex"=>"&#x0125;",
"heart"=>"&#x2665;",
"hebrewAlef"=>"&#x05D0;",
"hebrewAyin"=>"&#x05E2;",
"hebrewBet"=>"&#x05D1;",
"hebrewDalet"=>"&#x05D3;",
"hebrewGimel"=>"&#x05D2;",
"hebrewHe"=>"&#x05D4;",
"hebrewHet"=>"&#x05D7;",
"hebrewKaf"=>"&#x05DB;",
"hebrewKaffinal"=>"&#x05DA;",
"hebrewLamed"=>"&#x05DC;",
"hebrewMem"=>"&#x05DE;",
"hebrewMemfinal"=>"&#x05DD;",
"hebrewNun"=>"&#x05E0;",
"hebrewNunfinal"=>"&#x05DF;",
"hebrewPe"=>"&#x05E4;",
"hebrewPefinal"=>"&#x05E3;",
"hebrewQof"=>"&#x05E7;",
"hebrewResh"=>"&#x05E8;",
"hebrewSamekh"=>"&#x05E1;",
"hebrewShin"=>"&#x05E9;",
"hebrewTav"=>"&#x05EA;",
"hebrewTet"=>"&#x05D8;",
"hebrewTsadi"=>"&#x05E6;",
"hebrewTsadifinal"=>"&#x05E5;",
"hebrewVav"=>"&#x05D5;",
"hebrewYod"=>"&#x05D9;",
"hebrewZayin"=>"&#x05D6;",
"hookabovecomb"=>"&#x0309;",
"house"=>"&#x2302;",
"hstroke"=>"&#x0127;",
"hungarumlaut"=>"&#x02DD;",
"hyphen"=>"&#x002D;",
"iacute"=>"&#x00ED;",
"ibreve"=>"&#x012D;",
"icircumflex"=>"&#x00EE;",
"idiaeresis"=>"&#x00EF;",
"idieresis"=>"&#x00EF;",
"idotbelow"=>"&#x1ECB;",
"igrave"=>"&#x00EC;",
"ihook"=>"&#x1EC9;",
"ij"=>"&#x0133;",
"ijligature"=>"&#x0133;",
"imacron"=>"&#x012B;",
"infinity"=>"&#x221E;",
"integral"=>"&#x222B;",
"integralbt"=>"&#x2321;",
"integraltp"=>"&#x2320;",
"intersection"=>"&#x2229;",
"invbullet"=>"&#x25D8;",
"invcircle"=>"&#x25D9;",
"invsmileface"=>"&#x263B;",
"iogonek"=>"&#x012F;",
"iota"=>"&#x03B9;",
"iotadieresis"=>"&#x03CA;",
"iotadieresistonos"=>"&#x0390;",
"iotatonos"=>"&#x03AF;",
"itilde"=>"&#x0129;",
"jcircumflex"=>"&#x0135;",
"kappa"=>"&#x03BA;",
"kcommaaccent"=>"&#x0137;",
"kgreenlandic"=>"&#x0138;",
"kkra"=>"&#x0138;",
"lacute"=>"&#x013A;",
"lambda"=>"&#x03BB;",
"lcaron"=>"&#x013E;",
"lcommaaccent"=>"&#x013C;",
"ldot"=>"&#x0140;",
"ldotmiddle"=>"&#x0140;",
"leftguillemot"=>"&#x00AB;",
"less"=>"&#x003C;",
"lessequal"=>"&#x2264;",
"lfblock"=>"&#x258C;",
"lira"=>"&#x20A4;",
"logicaland"=>"&#x2227;",
"logicalnot"=>"&#x00AC;",
"logicalor"=>"&#x2228;",
"longs"=>"&#x017F;",
"lozenge"=>"&#x25CA;",
"lslash"=>"&#x0142;",
"lstroke"=>"&#x0142;",
"ltshade"=>"&#x2591;",
"macron"=>"&#x00AF;",
"male"=>"&#x2642;",
"minus"=>"&#x2212;",
"minute"=>"&#x2032;",
"mu"=>"&#x00B5;",
"multiply"=>"&#x00D7;",
"musicalnote"=>"&#x266A;",
"musicalnotedbl"=>"&#x266B;",
"nacute"=>"&#x0144;",
"napostrophe"=>"&#x0149;",
"ncaron"=>"&#x0148;",
"ncommaaccent"=>"&#x0146;",
"neng"=>"&#x014B;",
"nine"=>"&#x0039;",
"notelement"=>"&#x2209;",
"notequal"=>"&#x2260;",
"notsubset"=>"&#x2284;",
"ntilde"=>"&#x00F1;",
"nu"=>"&#x03BD;",
"numbersign"=>"&#x0023;",
"oacute"=>"&#x00F3;",
"obreve"=>"&#x014F;",
"ocircumflex"=>"&#x00F4;",
"ocircumflexacute"=>"&#x1ED1;",
"ocircumflexdotbelow"=>"&#x1ED9;",
"ocircumflexgrave"=>"&#x1ED3;",
"ocircumflexhook"=>"&#x1ED5;",
"ocircumflextilde"=>"&#x1ED7;",
"odiaeresis"=>"&#x00F6;",
"odieresis"=>"&#x00F6;",
"odotbelow"=>"&#x1ECD;",
"oe"=>"&#x0153;",
"oeligature"=>"&#x0153;",
"ogonek"=>"&#x02DB;",
"ograve"=>"&#x00F2;",
"ohook"=>"&#x1ECF;",
"ohorn"=>"&#x01A1;",
"ohornacute"=>"&#x1EDB;",
"ohorndotbelow"=>"&#x1EE3;",
"ohorngrave"=>"&#x1EDD;",
"ohornhook"=>"&#x1EDF;",
"ohorntilde"=>"&#x1EE1;",
"ohungarumlaut"=>"&#x0151;",
"omacron"=>"&#x014D;",
"omega"=>"&#x03C9;",
"omegatonos"=>"&#x03CE;",
"omicron"=>"&#x03BF;",
"omicrontonos"=>"&#x03CC;",
"one"=>"&#x0031;",
"onedotenleader"=>"&#x2024;",
"oneeighth"=>"&#x215B;",
"onehalf"=>"&#x00BD;",
"onequarter"=>"&#x00BC;",
"onesuperior"=>"&#x00B9;",
"onethird"=>"&#x2153;",
"openbullet"=>"&#x25E6;",
"ordfeminine"=>"&#x00AA;",
"ordmasculine"=>"&#x00BA;",
"orthogonal"=>"&#x221F;",
"oslash"=>"&#x00F8;",
"oslashacute"=>"&#x01FF;",
"ostroke"=>"&#x00F8;",
"otilde"=>"&#x00F5;",
"paragraph"=>"&#x00B6;",
"paragraphmark"=>"&#x00B6;",
"parenleft"=>"&#x0028;",
"parenright"=>"&#x0029;",
"partialdiff"=>"&#x2202;",
"percent"=>"&#x0025;",
"period"=>"&#x002E;",
"periodcentered"=>"&#x00B7;",
"perpendicular"=>"&#x22A5;",
"perthousand"=>"&#x2030;",
"peseta"=>"&#x20A7;",
"phi"=>"&#x03C6;",
"pi"=>"&#x03C0;",
"plus"=>"&#x002B;",
"plusminus"=>"&#x00B1;",
"prescription"=>"&#x211E;",
"product"=>"&#x220F;",
"propersubset"=>"&#x2282;",
"propersuperset"=>"&#x2283;",
"proportional"=>"&#x221D;",
"psi"=>"&#x03C8;",
"question"=>"&#x003F;",
"questiondown"=>"&#x00BF;",
"quotedbl"=>"&#x0022;",
"quotedblbase"=>"&#x201E;",
"quotedblleft"=>"&#x201C;",
"quotedblright"=>"&#x201D;",
"quoteleft"=>"&#x2018;",
"quotereversed"=>"&#x201B;",
"quoteright"=>"&#x2019;",
"quotesinglbase"=>"&#x201A;",
"quotesingle"=>"&#x0027;",
"quotesinglebase"=>"&#x201A;",
"racute"=>"&#x0155;",
"radical"=>"&#x221A;",
"rcaron"=>"&#x0159;",
"rcommaaccent"=>"&#x0157;",
"reflexsubset"=>"&#x2286;",
"reflexsuperset"=>"&#x2287;",
"registered"=>"&#x00AE;",
"revlogicalnot"=>"&#x2310;",
"rho"=>"&#x03C1;",
"rightguillemot"=>"&#x00BB;",
"ring"=>"&#x02DA;",
"rtblock"=>"&#x2590;",
"sacute"=>"&#x015B;",
"scaron"=>"&#x0161;",
"scedilla"=>"&#x015F;",
"scircumflex"=>"&#x015D;",
"scommaaccent"=>"&#x0219;",
"second"=>"&#x2033;",
"section"=>"&#x00A7;",
"sectionmark"=>"&#x00A7;",
"semicolon"=>"&#x003B;",
"seven"=>"&#x0037;",
"seveneighths"=>"&#x215E;",
"shade"=>"&#x2592;",
"sigma"=>"&#x03C3;",
"similar"=>"&#x223C;",
"six"=>"&#x0036;",
"slash"=>"&#x002F;",
"slong"=>"&#x017F;",
"smileface"=>"&#x263A;",
"softhyphen"=>"&#x00AD;",
"space"=>"&#x0020;",
"spade"=>"&#x2660;",
"ssharp"=>"&#x00DF;",
"sterling"=>"&#x00A3;",
"suchthat"=>"&#x220B;",
"summation"=>"&#x2211;",
"sun"=>"&#x263C;",
"tau"=>"&#x03C4;",
"tbar"=>"&#x0167;",
"tcaron"=>"&#x0165;",
"tcedilla"=>"&#x0163;",
"tcommaaccent"=>"&#x021B;",
"textacute"=>"&#x00B4;",
"textbreve"=>"&#x0306;",
"textbrokenbar"=>"&#x00A6;",
"textbullet"=>"&#x2022;",
"textcaron"=>"&#x030C;",
"textcedilla"=>"&#x00B8;",
"textcent"=>"&#x00A2;",
"textcircumflex"=>"&#x0302;",
"textcurrency"=>"&#x00A4;",
"textdag"=>"&#x2020;",
"textddag"=>"&#x2021;",
"textdegree"=>"&#x00B0;",
"textdiaeresis"=>"&#x0308;",
"textdiaresis"=>"&#x00A8;",
"textdiv"=>"&#x00F7;",
"textdotaccent"=>"&#x0307;",
"textellipsis"=>"&#x2026;",
"textgrave"=>"&#x0300;",
"texthungarumlaut"=>"&#x030B;",
"textlognot"=>"&#x00AC;",
"textmacron"=>"&#x00AF;",
"textminus"=>"&#x2012;",
"textmu"=>"&#x00B5;",
"textmultiply"=>"&#x00D7;",
"textogonek"=>"&#x0328;",
"textpm"=>"&#x00B1;",
"textring"=>"&#x030A;",
"textsterling"=>"&#x00A3;",
"texttilde"=>"&#x0303;",
"textunderscore"=>"&#x005F;",
"textyen"=>"&#x00A5;",
"therefore"=>"&#x2234;",
"theta"=>"&#x03B8;",
"thorn"=>"&#x00FE;",
"three"=>"&#x0033;",
"threeeighths"=>"&#x215C;",
"threequarter"=>"&#x00BE;",
"threequarters"=>"&#x00BE;",
"threesuperior"=>"&#x00B3;",
"tilde"=>"&#x02DC;",
"tildecomb"=>"&#x0303;",
"tonos"=>"&#x0384;",
"trademark"=>"&#x2122;",
"triagdn"=>"&#x25BC;",
"triaglf"=>"&#x25C4;",
"triagrt"=>"&#x25BA;",
"triagup"=>"&#x25B2;",
"tstroke"=>"&#x0167;",
"two"=>"&#x0032;",
"twodotenleader"=>"&#x2025;",
"twosuperior"=>"&#x00B2;",
"twothirds"=>"&#x2154;",
"uacute"=>"&#x00FA;",
"ubreve"=>"&#x016D;",
"ucircumflex"=>"&#x00FB;",
"udiaeresis"=>"&#x00FC;",
"udieresis"=>"&#x00FC;",
"udotbelow"=>"&#x1EE5;",
"ugrave"=>"&#x00F9;",
"uhook"=>"&#x1EE7;",
"uhorn"=>"&#x01B0;",
"uhornacute"=>"&#x1EE9;",
"uhorndotbelow"=>"&#x1EF1;",
"uhorngrave"=>"&#x1EEB;",
"uhornhook"=>"&#x1EED;",
"uhorntilde"=>"&#x1EEF;",
"uhungarumlaut"=>"&#x0171;",
"umacron"=>"&#x016B;",
"underscore"=>"&#x005F;",
"underscoredbl"=>"&#x2017;",
"union"=>"&#x222A;",
"universal"=>"&#x2200;",
"uogonek"=>"&#x0173;",
"upblock"=>"&#x2580;",
"upsilon"=>"&#x03C5;",
"upsilondieresis"=>"&#x03CB;",
"upsilondieresistonos"=>"&#x03B0;",
"upsilontonos"=>"&#x03CD;",
"uring"=>"&#x016F;",
"utilde"=>"&#x0169;",
"wacute"=>"&#x1E83;",
"wcircumflex"=>"&#x0175;",
"wdieresis"=>"&#x1E85;",
"weierstrass"=>"&#x2118;",
"wgrave"=>"&#x1E81;",
"xi"=>"&#x03BE;",
"yacute"=>"&#x00FD;",
"ycircumflex"=>"&#x0177;",
"ydiaeresis"=>"&#x00FF;",
"ydieresis"=>"&#x00FF;",
"ydotbelow"=>"&#x1EF5;",
"yen"=>"&#x00A5;",
"ygrave"=>"&#x1EF3;",
"yhook"=>"&#x1EF7;",
"ytilde"=>"&#x1EF9;",
"zacute"=>"&#x017A;",
"zcaron"=>"&#x017E;",
"zdotaccent"=>"&#x017C;",
"zero"=>"&#x0030;",
"zeta"=>"&#x03B6;",
"times"=>"&#215;",
"euml"=>"&#x00EB;",
"frasl"=>"&#x2044;",
"ne"=>"&#x2260;",
"middot"=>"&#x00B7;",
"sdot"=>"&#x22C5;",
"sol"=>"&#x002F;",
"ap"=> "&#x2248;",
"ouml"=>"&#246;",
"uuml"=>"&#252;",
"rarr"=>"&#8594;",
"larr"=>"&#8592;",
"prime"=>"&#8242;",
"Prime"=>"&#8243;",
"ang"=>"&#8736;",
"Nopf"=>"&#x2115;",
"Zopf"=>"&#x2124;",
"Qopf"=>"&#x211A;",
"Ropf"=>"&#x211D;",
"Copf"=>"&#x2102;",
"circ"=>"&#x02C6;",
"ge"=>"&#x2265;",
"le"=> "&#x2264;",
"iuml"=> "&#239;",
"plusmn"=> "&#177;",
"pm"=> "&#177;",
"xrarr"=> "&#x27F6;",
"xlarr"=> "&#x27F5;",
"or"=> "&#x2228;",
"and"=> "&#x2227;",
"nbsp"=> "&#160;",
"macr"=> "&#175;",
"centerdot"=> "&#x00B7;",
"rightarrow"=> "&#2192;",
"pound"=> "&#163;",
"euro"=> "&#8364;",
"thinsp"=> "&#8201;",
"ocirc"=>"&#244;",        
"asymp"=>"&#8776;",
"ndash"=>"&#8221;",
"cup"=>"&#8746;",
"cap"=>"&#8745;",
"apos"=>"&#39;",
"rang"=>"&#9002;",
"lang"=>"&#9001;",
"lcub"=>"&#x007B;",
"rcub"=>"&#x007D;",
"mdash"=>"&#8212;",
"sim"=>"&#8764;",
"cong"=>"&#8773;",
"Ocirc"=>"&#212;",
"ETH"=>"&#208;",
"hellip"=>"&#8230;",
"lsquo"=>"&#8216;",
"rsquo"=>"&#8217;",
"ccedil"=>"&#231;",
"raquo"=>"&#187;",
"ecirc"=>"&#202;",
"radic"=>"&#8730;",
"int"=>"&#8747;",
"infin"=>"&#8734;",
"sum"=>"&#8721;"    

                            );
    if(isset($table[$matches[1]])) {
        return $table[$matches[1]];
    } else {
        throw new Exception("Could not resolve entity ".print_r($matches,true));
    }
    }    
    
}


?>