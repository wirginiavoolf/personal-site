<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "stephanielanesutton@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "91537c" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'B1CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHUMdkMQCpjAGMDoEOgQgi7WyBrA2CDqIoKhjAIoxwsTATgqNWhW1dNXKrGlI7kNTBzUPlximHehuCQ1gDUV380CFHxUhFvcBAKz6yivik4h6AAAAAElFTkSuQmCC',
			'DFC4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHRoCkMQCpog0MDoENKKItYo0sDYItGKKMUwJQHJf1NKpYUuBVBSS+yDqgCZi6GUMDcG0A5tbUMRCA0QaGNDcPFDhR0WIxX0ASdnPcU70bXMAAAAASUVORK5CYII=',
			'A152' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHaY6IImxBjAGsDYwBAQgiYlMYQWKMTqIIIkFtAL1TgXKIbkvaikQZWatikJyH0gdkGxEtiM0FCzWyoBuXkPAFHQxRkeHAFQx1lCGUMbQkEEQflSEWNwHAL21ynfp+xhaAAAAAElFTkSuQmCC',
			'F558' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHaY6IIkFNIg0sDYwBARgiDE6iKCKhbBOhasDOyk0aurSpZlZU7OQ3AeUb3RoCEAzDyQWiG5eoyuGGGsro6MDml7GEIZQBhQ3D1T4URFicR8AAMrNuELIbqEAAAAASUVORK5CYII=',
			'B252' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHaY6IIkFTGFtZW1gCAhAFmsVaXRtYHQQQVHH0Og6laFBBMl9oVGrli7NzFoVheQ+oLopQLIRxY5WhgAwiSLG6MAKUo3qlgZGR4cAVDeLhjqEMoaGDILwoyLE4j4AI1zNo9+fsrIAAAAASUVORK5CYII=',
			'4D2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMIQyhgYgi4WItDI6Ojogq2MMEWl0bQhEEWOdItLogBADO2natGkrs1ZmhmYhuS8ApK6VEUVvaChQbAqqGANIXQCGGFAnuphoCGtoIKqbByr8qAexuA8Akm3KIT9RsJYAAAAASUVORK5CYII=',
			'2BF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZW1gCAhAEgtoFWl0bWB0EEHW3QpW1yCC7L5pU8OWhq5aFYXsvgCwukZkO4AmAc1jaEVxSwNYbAqymEgDxC3IYqGhQDc3MIaGDILwoyLE4j4AcwDLggTQi5YAAAAASUVORK5CYII=',
			'F8F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1qRxQIaWFtZGximooqJNLo2MIRiUQfTC3ZSaNTKsKWhq5Yiuw9NHbJ5RIhh0wt0M9AtAYMg/KgIsbgPAPs1zOa9Lq0SAAAAAElFTkSuQmCC',
			'DD2A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGVqRxQKmiLQyOjpMdUAWaxVpdG0ICAhAE3NoCHQQQXJf1NJpK7NWZmZNQ3IfWF0rI0wdQmwKY2gIulgAmjqQWxxQxUBuZg0NRBEbqPCjIsTiPgAxX82Kp/vwewAAAABJRU5ErkJggg==',
			'6C4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxkaHUMdkMREprA2OrQ6OgQgiQW0iDQ4THV0EEEWawDyAuFiYCdFRk1btTIzM2sakvtCpog0sDai6W0FioUGYog5oKkDu6UR1S3Y3DxQ4UdFiMV9AE8WzRvTrnA3AAAAAElFTkSuQmCC',
			'7A47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHUNDkEVbGUMYWh0aRFDEWFsZpqKJTRFpdAh0aAhAdl/UtJWZmVkrs5Dcx+gg0uja6NCKbC9rg2ioa2jAFGQxkQageY0OAchiAWAxRwdCYgMVflSEWNwHAF/+zU0eZ4m9AAAAAElFTkSuQmCC',
			'4A5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjAEsIY6hoYgi4UwhrA2MDogqwOKtKKLsU4RaXSdChcDO2natGkrUzMzQ7OQ3BcAVOfQEIiiNzRUNBRdjAFkHhYxR0dHDDGHUFS3DFj4UQ9icR8AJ/TKCqhoxuoAAAAASUVORK5CYII=',
			'5D08' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEESCwwQaXRtCICpAzspbNq0lamroqZmIbuvFUUdklgginkBrZh2iEzBdAtrAKabByr8qAixuA8Av7DNT3rNyX4AAAAASUVORK5CYII=',
			'104F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUNDkMRYHRhDGFodHZDViTqwtjJMRRVjdBBpdAiEi4GdtDJr2srMzMzQLCT3gdS5NmLqdQ0NRBMD2oGhDugWNDHRELCbUcQGKvyoCLG4DwAor8dO7e5S5AAAAABJRU5ErkJggg==',
			'3EE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7RANEQ1lDHaY6IIkFTBFpYG1gCAhAVtkKEmN0EEEWQ1UHdtLKqKlhS0NXTc1Cdh+x5mERw+YWbG4eqPCjIsTiPgC+GMsFrKXf+AAAAABJRU5ErkJggg==',
			'ED85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUMDkMQCGkRaGR0dHRhQxRpdGwIxxBwdHV0dkNwXGjVtZVboyqgoJPdB1Dk0iGCYF4BFLNBBBMMtDgHI7oO4mWGqwyAIPypCLO4DAFv6zVWpICh2AAAAAElFTkSuQmCC',
			'7126' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGaY6IIu2MgYwOjoEBKCIsQawNgQ6CCCLTQHqBYqhuC8KCFdmpmYhuY/RAagOaCayeawNQLEpjA4iSGIiILEAVDGgngCQ/gAUMdZQ1tAAVDcPUPhREWJxHwCbbsiXc7G24wAAAABJRU5ErkJggg==',
			'E440' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYWhkaHVqRxQIaGKYytDpMdUAVC2WY6hAQgCLG6MoQ6OggguS+0KilS1dmZmZNQ3JfQINIK2sjXB1UTDTUNTQQTQzsFjQ7wGIobsHm5oEKPypCLO4DAC8VzdpcLJMWAAAAAElFTkSuQmCC',
			'BD7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA0MDkMQCpoi0MjQEOiCrC2gVaXRAF5sCFGt0hImBnRQaNW1l1tKVoVlI7gOrm8KIaV4AppijAyO6Ha2sDahiYDc3MKK4eaDCj4oQi/sA55LMg7gBmHcAAAAASUVORK5CYII=',
			'CCD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDGaY6IImJtLI2ujY6BAQgiQU0ijS4NgQ6iCCLNYg0sDYEwNSBnRS1atqqpauipmYhuQ9NHZIYmnlY7MDmFmxuHqjwoyLE4j4A5dDOW5SSHKcAAAAASUVORK5CYII=',
			'31F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA1qRxQKmMAawNjBMRVHZygoSC0URm8IAEoPpBTtpZdSqqKWhq5aiuA9VHdQ84sQCsOgVBboY5JaAQRB+VIRY3AcAGSPI7fm349oAAAAASUVORK5CYII=',
			'7787' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUNDkEVbGRodHR0aRNDEXBsCUMWmMLQyAtUFILsvatW0VaGrVmYhuY/RgSEAqK4V2V5WoChrQ8AUZDERoChQLABZDGQjI9Ax6GIMoYwoYgMVflSEWNwHAEyDyzriTtfsAAAAAElFTkSuQmCC',
			'54A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMDQEIIkB2VMZQhka0cRCGR0dWpHFAgMYXVkbAqYEILkvbNrSpUtXRUVFIbuvVaSVtSHQAVkvQ6toqGtoYGgIsh2tDEB1AShuEZmCKcYagCk2UOFHRYjFfQDK0s5uHWaAiAAAAABJRU5ErkJggg==',
			'2222' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEHW3crQ6NAQ0CCC7L5pq5auWpm1KgrZfQEMUyBqEXoZHaCiyG6BiSK7BSaKJBYaKhrqGhoYGjIIwo+KEIv7AAWbyxH3nqnlAAAAAElFTkSuQmCC',
			'414D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37pjAEMDQ6hjogi4UwBjC0OjoEIIkxhrAGMEx1dBBBEmMF6Q2Ei4GdNG3aqqiVmZlZ05DcFwBUx9qIqjc0FCgWGogiBnULVrEAFDHWUAw3D1T4UQ9icR8AuwnJjCTKJGAAAAAASUVORK5CYII=',
			'8217' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIaGIImJTGFtZQgB0khiAa0ijY5oYiJTGBodpgDlkNy3NGrV0lXTVq3MQnIfUB0ItjKgmMcQABZFEWN0AIoEMKC6pQHoPgdUN4uGOoY6oogNVPhREWJxHwB44stsh0LMEQAAAABJRU5ErkJggg==',
			'D96A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpdG1wCAjAEGN0EEFyX9TSpUtTp67MmobkvoBWxkBXR0eYOqgYA1BvYGgIihgLSAxVHdgtqHohbmZEERuo8KMixOI+AOv1zWdrCXBkAAAAAElFTkSuQmCC',
			'F407' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZWhmmMIaGIIkFNDBMZQhlaBBBFQtldHRAE2N0ZQWSAUjuC41aunTpqqiVWUjuC2gQaQWqa2VA0Ssa6toQMAVVjKEVaEcAuhjQZgcMsSmoYgMVflSEWNwHANr/zJPJpMAGAAAAAElFTkSuQmCC',
			'91C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHVqRxUSmMAYwOgRMdUASC2hlDWBtEAgIQBFjAIoxOogguW/a1FVRS1etzJqG5D5WVxR1ENiKKSYAFkO1Q2QKA4ZbgC4JRXfzQIUfFSEW9wEAsqLJUnKo3/AAAAAASUVORK5CYII=',
			'64F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYWllDA0MDkMREpjBMZW1gdEBWF9DCEIoh1sDoChRzdUByX2TU0qVLQ1dGRSG5L2SKSCsryFxkva2ioa4YYkC3AO1AFgO6BaQ3ANl9YDc3MEx1GAThR0WIxX0AMn7KyH+2+AcAAAAASUVORK5CYII=',
			'BDD0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDGVqRxQKmiLSyNjpMdUAWaxVpdG0ICAhAVQcUC3QQQXJfaNS0lamrIrOmIbkPTR2SedjEMOzAcAs2Nw9U+FERYnEfAFCOz4NrsTd7AAAAAElFTkSuQmCC',
			'A8B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWl0bQh0EEASC2gFqXN0QHZf1NKVYUtDV6ZmIbkPqg7FvNBQiHkiKOZhE8N0S0ArppsHKvyoCLG4DwD7g80vdQKa+QAAAABJRU5ErkJggg==',
			'D804' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMDQEIIkFTGFtZQhlaEQRaxVpdHR0aEUVY21lBaoOQHJf1NKVYUtXRUVFIbkPoi7QAd0814bA0BBMO7C5BUUMm5sHKvyoCLG4DwBVYc+MRCWNKQAAAABJRU5ErkJggg==',
			'3C42' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYQxkaHaY6IIkFTGFtdGh1CAhAVtkq0uAw1dFBBFlsCpAX6NAgguS+lVHTVq3MzFoVhew+oDqgiY0OaOaxhga0MqDb0egwhQHdLY0OAZhudgwNGQThR0WIxX0APADNxH6gYXgAAAAASUVORK5CYII=',
			'5984' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGRoCkMQCGlhbGR0dGlHFRBpdGwJakcUCA0QaHR0dpgQguS9s2tKlWaGroqKQ3dfKGOgIVIisl6GVAWheYGgIsh2tLCA7UNwiMgXsFhQx1gBMNw9U+FERYnEfALVAzhpAiZ87AAAAAElFTkSuQmCC',
			'F5BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGaYGIIkFNIg0sDY6BIigizUEOrCgioWwNjo6ILsvNGrq0qWhK7OQ3Qc0u9EVoQ4hBjQPVUwELIZqB2srplsYQ9DdPFDhR0WIxX0AoV7NbyD8xKUAAAAASUVORK5CYII=',
			'979B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMdkMREpjA0Ojo6OgQgiQW0MjS6NgQ6iKCKtbICxQKQ3Ddt6qppKzMjQ7OQ3MfqyhDAEBKIYh5DK6MDA5p5AkDTGNHERKaINDCiuYU1AKgCzc0DFX5UhFjcBwDUMcrjUJr7WAAAAABJRU5ErkJggg==',
			'4087' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjAEMIQyhoYgi4UwhjA6OjSIIIkxhrC2sjYEoIixThFpdASqC0By37Rp01Zmha5amYXkvgCIulZke0NDRRpdgTKobgHbEYAqBnKLowMWN6OKDVT4UQ9icR8AuqbK7dtXh5EAAAAASUVORK5CYII=',
			'E2B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGaY6IIkFNLC2sjY6BASgiIk0ujYEOoigiDE0ujY6wsTATgqNWrV0aeiqqDAk9wHVTQGaNxVNbwAryFQUMUYHoBiaHawN6G4JDRENdUVz80CFHxUhFvcBADwyzdjhGhxNAAAAAElFTkSuQmCC',
			'4D51' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGsIY6tKKIhYi0sjYwTEUWYwwRaXRtYAhFFmOdAhSbygDTC3bStGnTVqZmZi1Fdl8AUJ1DQwCKHaGhmGIMIPMwxVoZHdHcB3Qz0CWhAYMh/KgHsbgPAK7RzMGns1RhAAAAAElFTkSuQmCC',
			'3B48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANEQxgaHaY6IIkFTBFpZWh1CAhAVtkqAlTl6CCCLAZSFwhXB3bSyqipYSszs6ZmIbsPqI61EdM819BAVPNAdjSi2gF2C5pebG4eqPCjIsTiPgDECc167LdKkQAAAABJRU5ErkJggg==',
			'6FE7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUNDkMREpog0sIJoJLGAFixiDRCxACT3RUZNDVsaumplFpL7QiDmtSLbG9AKFpuCRSwAWQziFkYHVDcDxUIdUcQGKvyoCLG4DwCii8t+dNEAOQAAAABJRU5ErkJggg==',
			'BCF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDAxoCkMQCprA2ujYwNKKItYo0AMVaUdWJNLA2MEwJQHJfaNS0VUtDV0VFIbkPoo7RAd08oFhoCKYd2NyCIgZ2M5rYQIUfFSEW9wEANf3PXsELgngAAAAASUVORK5CYII=',
			'DA87' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUNDkMQCpjCGMDo6NIggi7WytrI2BKCJiTQ6AtUFILkvaum0lVmhq1ZmIbkPqq6VAUWvaKgr0CYGNPOAYgEoYlNAeh0dUN0s0ugQyogiNlDhR0WIxX0AXj3OAEgSIDYAAAAASUVORK5CYII=',
			'B1F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0NDkMQCpjAGsAJpEWSxVlZMsSkMYLEAJPeFRq2KWhq6amUWkvug6loZUMwDi03BIhbAgGEHowOqm1lD0cUGKvyoCLG4DwAweMpNR5m7sAAAAABJRU5ErkJggg==',
			'FF0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMIaGIIkFNIg0MIQyOjCgiTE6OmKIsTYEwsTATgqNmhq2dFVkaBaS+9DU4RXDZgc2tzBMQRUbqPCjIsTiPgCNEcrHy+BkngAAAABJRU5ErkJggg==',
			'1CA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMIaGIImxOrA2OoQyNIggiYk6iDQ4OjqgiDECxVgbAoAQ4b6VWdNWLV0VtTILyX1Qda0M6HpDA6agi7k2BASgirE2ujYEOiCLiYYwhrKiiQ1U+FERYnEfAMjQykqpAhAOAAAAAElFTkSuQmCC',
			'8F62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoFWlgbXB0EEFTxwqikdy3NGpq2NKpq1ZFIbkPrM7RodEBw7yAVgZMsSkMWNyC6magjaGMoSGDIPyoCLG4DwCrq8yKkTcCVAAAAABJRU5ErkJggg==',
			'CA00' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WEMYAhimMLQii4m0MoYwhDJMdUASC2hkbWV0dAgIQBZrEGl0bQh0EEFyX9SqaStTV0VmTUNyH5o6qJhoKIZYo0ijI5odIq0ijQ5obmENAYqhuXmgwo+KEIv7ABPUzTZk7CQCAAAAAElFTkSuQmCC',
			'6734' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2Quw2AMAxEjyIbhH1CQW+kpCDT2BLZIDBEpuRTxUAJAl/3pLOfjHIZxp/yip+hNnQBTBWzGdKLk5rRBHFMSTFG2mimym+MZSlzibHy8xkE6ZzqpsaBh+AVM8dW7WLZ7JeVs+Xm5PzV/x7Mjd8KBM3PKWxBTHQAAAAASUVORK5CYII=',
			'921C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDCECCCJBbQKtLoGMLowIIixtDoMIXRAdl906auWrpq2sosZPexugJtQKiDwFaGAHQxgVYgfwqqHUC3NAB1o7iFNUA01DHUAcXNAxV+VIRY3AcAt+DKKe/WYqYAAAAASUVORK5CYII=',
			'D92F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUNDkMQCprC2Mjo6OiCrC2gVaXRtCMQQc0CIgZ0UtXTp0qyVmaFZSO4LaGUMdGhlRNPL0OgwBV2MpdEhAE0M5BYHVDGQm1lDUd0yUOFHRYjFfQAMdcsM9XFmeQAAAABJRU5ErkJggg==',
			'5162' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM3QsQ2AIBCF4UfhBgwEGzwTz8JpzoINwA1smFKizRktNeGu+0LCn0N9jKKn/aVPJhCCEoxRHV0M5M0GDhqDNzYSzaDe9M1bXfZS62L7UnsXw2r/OE2ZbAsvy9Z8xtlirZUIxMnUwf0+3Je+A9I6yiOsvlYTAAAAAElFTkSuQmCC',
			'8D6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gVaXRtcAgIQFUHFGN0EEFy39KoaStTp67MmobkPrA6R0eYOiTzAkNDMMVQ1EHcgqoX4mZGFLGBCj8qQizuAwDXU8yXXzpOTgAAAABJRU5ErkJggg==',
			'A3D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGUIdkMRYA0RaWRsdHQKQxESmMDS6NgQ0iCCJBbQytLICxQKQ3Be1dFXYUiCZheQ+NHVgGBqK1TwsYphuCWjFdPNAhR8VIRb3AQB1bM5V2lvZnAAAAABJRU5ErkJggg==',
			'A672' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nM2QsQ3EMAhFccEGZB9ugx/JvuKmwQUbJNkgjaeMUx3WpcxJgQLxBOgJaj9h9KT8i1/SlLlg1cAY7GQAApNFKtmsEhi8d1VNgt9n395t7zX4wSen5Zz87pYiVUFO47360j45MHY2wsi6s6WSH/C/G/PC7wD19czbUhyDEgAAAABJRU5ErkJggg==',
			'C52F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WENEQxlCGUNDkMREWkUaGB0dHZDVBTSKNLA2BKKKNYiEMCDEwE6KWjV16aqVmaFZSO4LaGBodGhlRNMLFJvCiG5Ho0MAqphIKytQJ6oYawhjCGsoqlsGKvyoCLG4DwA9tcmsy6W3lgAAAABJRU5ErkJggg==',
			'8EDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUMdkMREpog0sDY6OgQgiQW0AsUaAh1E0NUhxMBOWho1NWzpqsisaUjuQ1OH0zycdqC5BZubByr8qAixuA8AyaXL5i0sFyEAAAAASUVORK5CYII=',
			'94B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QLQ7AIAxGi6ifYPfB4LtkNTsNiN4AdgMMpxy4Eia3hH7upT8vhTpVgJXyix8SCDJkp5hNkDE6IsVIgDEczg7M+NYXrPK7cymFa72UH3orrS/qGyA7+75VsU2aS6AEo0ufpdnZ8LnA/z7Mi98DnVLMdIeu2iwAAAAASUVORK5CYII=',
			'988B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6NgQ6iKCIoagDO2na1JVhq0JXhmYhuY/VFdM8BizmCWARw+YWbG4eqPCjIsTiPgCGMsrVhrlySAAAAABJRU5ErkJggg==',
			'1D82' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxOoi0Mjo6BAQgiYk6iDS6NgQ6iKDoFWl0dHRoEEFy38qsaSuzQletikJyH1RdowOaXteGgFYGTLEpaGJgtyCLiYaA3MwYGjIIwo+KEIv7AJhnyiNsSqaDAAAAAElFTkSuQmCC',
			'C2DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUMDkMREWllbWRsdHZDVBTSKNLo2BKKKNTAgi4GdFLVq1dKlqyJDs5DcB1Q3hRVTbwCGWCOjA7oY0C0N6G5hDRENdUVz80CFHxUhFvcBABI4y0CKmzukAAAAAElFTkSuQmCC',
			'9511' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMLQii4lMEWlgCGGYiiwW0CrSwBjCEIomFoKkF+ykaVOnLl01bdVSZPexujI0OqDZAeRhiAm0imCIiUxhbUV3H2sAYwhjqENowCAIPypCLO4DADsHy4VUx4IAAAAAAElFTkSuQmCC',
			'C70B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WENEQx2mMIY6IImJtDI0OoQyOgQgiQU0MjQ6Ojo6iCCLNTC0sjYEwtSBnRS1atW0pasiQ7OQ3AeUD0BSBxVjdACJoZjXyNrAiGaHSCuQh+YW1hCgGJqbByr8qAixuA8ApVfLn9n/VvYAAAAASUVORK5CYII=',
			'68C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHaY6IImJTGFtZXQICAhAEgtoEWl0bRB0EEEWa2BtZQWpR3JfZNTKsKVAOgrJfSFTwOoake0IaAWZx9DKgCEmMIUBi1sw3ewYGjIIwo+KEIv7ANruzLIW3WMWAAAAAElFTkSuQmCC',
			'95D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGVqRxUSmiDSwNjpMRRYLaAWKNQSEoomFAMVgesFOmjZ16tKlq6KWIruP1ZWh0RWhDgJbMcUEWkUwxESmsLYC3YIixhrAGAJ0c2jAIAg/KkIs7gMAHwjM9nIEDA8AAAAASUVORK5CYII=',
			'2159' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYG1gCAhAEgtoZQWKMTqIIOtuBeqdCheDuGnaqqilmVlRYcjuA9oBNGEqsl5GB7BYA7IYyE7WhgAUO4DsAEZHBxS3hIayhjKEMqC4eaDCj4oQi/sAEIfI8QpGrMIAAAAASUVORK5CYII=',
			'392E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUMDkMQCprC2Mjo6OqCobBVpdG0IRBWbItLogBADO2ll1NKlWSszQ7OQ3TeFMdChlRHNPIZGhynoYiyNDgGoYmC3OKCKgdzMGhqI4uaBCj8qQizuAwDymMl/tL6cuwAAAABJRU5ErkJggg==',
			'A27A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgDWIH8gKkOSGIiU0QaHRoCAgKQxIC6Gh0aHR1EkNwXtXQVEK7MmobkPqC6KQxTGGHqwDA0lCGAIYAxNATFPKBrHFDVBbSyNrA2oIuJhrqiiQ1U+FERYnEfABzVy/KtBeiCAAAAAElFTkSuQmCC',
			'6937' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6NIggiQW0iABFAlDFGoBiYFGE+yKjli7NmrpqZRaS+0KmMAYC1bUi2xvQygDSOQVVjAUkFsCA4RZHByxuRhEbqPCjIsTiPgAEm81rhSoNJwAAAABJRU5ErkJggg==',
			'0A74' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDAxoCkMRYAxhDGBoCGpHFRKawtgLFWpHFAlpFGh0aHaYEILkvaum0lVlLV0VFIbkPrG4KowOqXtFQhwDG0BAUO0QaHR0Y0Nwi0ujagCrG6IApNlDhR0WIxX0Aly/OMrx9ksIAAAAASUVORK5CYII=',
			'69D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMRRYLaBFpdG0ICEURawCLwfSCnRQZtXRp6qqopcjuC5nCGIikDqK3laERU4wFQwzqFhQxqJtDAwZB+FERYnEfABZzzcZXE0p5AAAAAElFTkSuQmCC',
			'3CB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RAMYQ1lDGaY6IIkFTGFtdG10CAhAVtkq0uDaEOgggiw2RaSBFaEO7KSVUdNWLQ1dNTUL2X2o6uDmsaKbh8UObG7B5uaBCj8qQizuAwDgc82K5W+kbAAAAABJRU5ErkJggg==',
			'717B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MdkEVbGQMYGgIdAlDEWMFiIshiUxgCGBodYeogbooCwqUrQ7OQ3MfoAFQ3hRHFPNYGoFgAI4p5QDZQBFUMqCeAtQFVb0ADayhQDNXNAxR+VIRY3AcAKrfI1cPmFPYAAAAASUVORK5CYII=',
			'6DDC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGaYGIImJTBFpZW10CBBBEgtoEWl0bQh0YEEWa4CIIbsvMmraytRVkVnI7guZgqIOorcVtxiyHdjcgs3NAxV+VIRY3AcAoCLNXv5XZ9AAAAAASUVORK5CYII=',
			'55ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHUMdkMQCGkQaWBsYHQKwiIkgiQUGiIQgiYGdFDZt6tKloSuzpiG7r5Wh0RVNLzaxgFYRDDGRKayt6G5hDWAMQXfzQIUfFSEW9wEAokHK2J8LgAQAAAAASUVORK5CYII=',
			'7FF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA1pRRFtFGlgbGKZiEQtFEZsCFoPphbgpamrY0tBVS5Hdx+iAog4MWRswxUSwiAXgFgsNGAThR0WIxX0A3rXLOLVs8vUAAAAASUVORK5CYII=',
			'D950' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMdUAWaxVpdG1gCAhAF5vK6CCC5L6opUuXpmZmZk1Dcl9AK2OgQ0MgTB1UjKERU4wFaEcAqh1AtzA6OqC4BeRmhlAGFDcPVPhREWJxHwB9aM4Dl5pBqAAAAABJRU5ErkJggg==',
			'5C48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQxkaHaY6IIkFNLA2OrQ6BASgiIk0OEx1dBBBEgsMAPIC4erATgqbNm3VysysqVnI7msVAZmIYh5YLDQQxbwAoJhDI6odIlOAOtH0sgZgunmgwo+KEIv7AMNTzhz/LsR4AAAAAElFTkSuQmCC',
			'A2AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMIaGIImxBrC2MoQyOiCrE5ki0ujo6IgiFtDK0OjaEAgTAzspaumqpUtXRYZmIbkPqG4KK0IdGIaGMgSwhgaimcfogK4uoJW1AVNMNNQVTWygwo+KEIv7AAVcysprZVQCAAAAAElFTkSuQmCC',
			'7670' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1pRRFtZgfyAqQ4oYiKNQLGAAGSxKSINDI2ODiLI7ouaFrZq6cqsaUjuY3QQbWWYwghTB4asDSKNDgGoYiJAMUcHBhQ7AhpYW1kbGFDcEtAAdDPQRYMh/KgIsbgPAMk0y81Z8RjOAAAAAElFTkSuQmCC',
			'AD4E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHUMDkMRYA0RaGVodHZDViUwRaXSYiioW0AoUC4SLgZ0UtXTayszMzNAsJPeB1Lk2ouoNDQWKhQZimteIYUcrA4YYppsHKvyoCLG4DwAlvcxzeSI1agAAAABJRU5ErkJggg==',
			'61CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHUMDkMREpjAGMDoEOiCrC2hhDWBtEEQVa2AAijHCxMBOioxaFbV01crQLCT3hUxBUQfR24pLDNUOEaBedLcAXRKK7uaBCj8qQizuAwDMAcfhDGsBHgAAAABJRU5ErkJggg==',
			'1BB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGaY6IImxOoi0sjY6BAQgiYk6iDS6NgQ6iKDoBatrEEFy38qsqWFLQ1etikJyH1RdowOqXqB5Aa0MmGJTGDDtCEAWEw0BuZkxNGQQhB8VIRb3AQBCD8qsmUqh9wAAAABJRU5ErkJggg==',
			'667A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsAL5AVMdkMQCWkQagWRAALJYg0gDQ6OjgwiS+yKjpoWtWroyaxqS+0KmiLYyTGGEqYPobRVpdAhgDA1BE3N0QFUHcgtrA6oY2M1oYgMVflSEWNwHABNry8Ti/zT2AAAAAElFTkSuQmCC',
			'5781' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGVqRxQIaGBodHR2moou5NgSEIosFBjC0Mjo6wPSCnRQ2bdW0VaGrlqK4r5UhAEkdVIzRgbUhANXeVtYGdDGRKSIN6HpZA0QaGEIZQgMGQfhREWJxHwAzq8wCLxlQdQAAAABJRU5ErkJggg==',
			'8927' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AMAwE3xLZwAOZgt5IpGEEpkiKbOAVUsCUpEwCJQh83RWvk3FcLuBPvNLnlBZ48kvl2FyiUQJXThPHKWjj2DhKcVr15TXnbS9UfWw0S0JCs4coBmvdEEWh6FuEpG92fm7cV/97kJu+ExWEy8p+UUefAAAAAElFTkSuQmCC',
			'C33F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WENYQxhDGUNDkMREWkVaWRsdHZDVBTQyNDo0BKKKNTC0MiDUgZ0UtWpV2KqpK0OzkNyHpg4mhmkeFjuwuQXqZhSxgQo/KkIs7gMAyefK+Wt/5Y0AAAAASUVORK5CYII=',
			'154C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHaYGIImxOog0MLQ6BIggiYmCxKY6OrCg6BUJYQh0dEB238qsqUtXZmZmIbuP0YGh0bURrg4hFhqIJibS6NCIbgdrK9B9qG4JYQxBd/NAhR8VIRb3AQCMc8le2HeFiwAAAABJRU5ErkJggg==',
			'CF1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQx2mMIaGIImJtIo0MIQwOiCrC2gUaWBEF2sAqpsCFwM7KWrV1LBV01aGZiG5D00dbrFGTDGwW9DEWEOAbgl1RBEbqPCjIsTiPgD2u8mLBFJTfQAAAABJRU5ErkJggg==',
			'36CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCHVqRxQKmsLYyOgRMdUBW2SrSyNogEBCALDZFpIG1gdFBBMl9K6OmhS1dtTJrGrL7poi2IqmDm+fawBgagiEmiKIO4pZAFDGImx1RzRug8KMixOI+ANDfyviFrkOVAAAAAElFTkSuQmCC',
			'0FAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMIYGIImxBog0MIQyOiCrE5ki0sDo6IgiFtAq0sDaEAgTAzspaunUsKWrIkOzkNyHpg4hFhqIYQe6OpBb0MUYHcBiKG4eqPCjIsTiPgBu2coyRzIUUAAAAABJRU5ErkJggg==',
			'647D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYWllDA0MdkMREpjBMZWgIdAhAEgtoYQgFiYkgizUwujI0OsLEwE6KjFq6dNXSlVnTkNwXMkWklWEKI6reVtFQhwB0MYZWRgdUMaBbWlkbGFHcAnZzAyOKmwcq/KgIsbgPAB9zyyi/eKA6AAAAAElFTkSuQmCC',
			'F805' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQximMIYGIIkFNLC2MoQyOjCgiIk0Ojo6oomxtrI2BLo6ILkvNGpl2NJVkVFRSO6DqAOagGaeKxYxkB0iGG5hCEB1H8jNDFMdBkH4URFicR8Am1rMxaUvgCsAAAAASUVORK5CYII=',
			'01A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIaGIImxBjAGMIQyNIggiYlMAYo6OqCIBbQyBLA2BAAhwn1RS8FoZRaS+6DqWhnQ9YYGTGFAsQOsLoABxS0gsUAHVDezhqKLDVT4URFicR8ADk3JsB5/CEIAAAAASUVORK5CYII=',
			'B2D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaXRtCHQQQVHHABQLgKkDOyk0atXSpauipmYhuQ+obgorQh3UPIYAVnTzWhkdMMSAOtHdEhogGuqK5uaBCj8qQizuAwBR0c6lPiVeTAAAAABJRU5ErkJggg==',
			'4D72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37poiGsIYGTHVAFgsRaWVoCAgIQBJjDBFpdGgIdBBBEmOdAhQDiooguW/atGkrs5auWhWF5L4AkLopDI3IdoSGAsUCGFpR3SLS6OjAMAVNrJW1gSEAw80NjKEhgyH8qAexuA8Ap3DNLG69bT0AAAAASUVORK5CYII=',
			'F501' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMLQiiwU0iDQwhDJMRRdjdHQIRRMLYW0IgOkFOyk0aurSpauiliK7L6CBodEVoQ6PmEijo6MDmhhrK9AtaGKMIUA3hwYMgvCjIsTiPgCwb82IBSsGTwAAAABJRU5ErkJggg==',
			'BBD6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDGaY6IIkFTBFpZW10CAhAFmsVaXRtCHQQQFcHFEN2X2jU1LClqyJTs5DcB1WH1TwRQmJY3ILNzQMVflSEWNwHACBxzprkK6LqAAAAAElFTkSuQmCC',
			'967A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsAL5AVMdkMQCWkUagWRAAKpYA0Ojo4MIkvumTZ0WtmrpyqxpSO5jdRVtZZjCCFMHgUDzHAIYQ0OQxASAYo4OqOpAbmFtQBUDuxlNbKDCj4oQi/sAVi7LFLTkGRwAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>