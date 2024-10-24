<?php
# PHP code to redirect the old path to the new path
# This file must be manually copied to the root folder of the website
#
# Also, make sure that the path view?comp=... is redirected to this file view.php?comp=...
# This can be done with the following .htaccess rule:
#   RewriteEngine On
#   #  Match any URL path that starts with /view
#   RewriteCond %{QUERY_STRING} (.+)
#   RewriteRule ^view$ /view.php?%{QUERY_STRING} [L,R=301]

# Example: 
# FROM: view?comp=bt-gr1&subcomp=bt-gr111&variant=m4a_view_mbo&item=example&num=1&sector=ict
# TO: mbo/content/ict/bt-gr1/bt-gr11/bt-gr11-example-1.html

$VARIANT_MAP = array(
    'm4a_view' => array(
        'root' => 'sec',
        'sector' => false
    ),
    'm4a_view_en' => array(
        'root' => 'sec_en',
        'sector' => false
    ),
    'm4a_view_mbo' => array(
        'root' => 'mbo',
        'sector' => true
    ),
    'basis_wm' => array(
        'redirect' => 'http://wm.math4allview.appspot.com/view?'
    )
);

// Get the parameters from the URL
$comp = $_GET['comp'];
$subcomp = $_GET['subcomp'];

$variant = 'm4a_view';
if(isset($_GET['variant'])) {
    $variant = $_GET['variant'];
} elseif(isset($_GET['repo'])) {
    switch($_GET['repo']) {
        case 'm4a_en': 
            $variant = 'm4a_view_en';
            break;
    }
}

if(!isset($_GET['item'])) {
    $item = 'index';
} else {
    $item = "$subcomp-".$_GET['item'];
}

if (isset($_GET['num'])) {
    $num = $_GET['num'];
} else {
    $num = '';
}

if (isset($_GET['sector'])) {
    $sector = $_GET['sector'];
} else {
    $sector = 'no-sector';
}

if (isset($_GET['parent'])) {
    $parent = $_GET['parent'];
} else {
    $parent = null;
}

$variant_spec = $VARIANT_MAP[$variant];
if (!$variant_spec) {
    $variant_spec = $VARIANT_MAP['m4a_view'];
}

$new_path = '';
if($variant_spec['redirect']) {
    $new_path = $variant_spec['redirect'].$_SERVER['QUERY_STRING'];
} else {
    $root = $variant_spec['root'];

    // Create the new path
    $new_path = "$root/content";
    if($variant_spec['sector']) {
        
        $new_path .= "/$sector";
    }
    $new_path .= "/$comp/$subcomp/$item";
    if($num) {
        $new_path .= "-$num";
    }
    $new_path .= ".html";
    
    if($parent!==null) {
        $new_path .= "?parent=$parent";
    }
    
}

// Redirect to the new path
header("Location: $new_path");

?>
