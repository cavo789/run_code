<?php
/**
 * Run PHP Code
 *
 * This script gives you the ability to quickly test snippets of PHP code locally.
 *
 * @copyright Copyright 2011-2014, Website Duck LLC (http://www.websiteduck.com)
 * @link      http://github.com/websiteduck/Run-PHP-Code Run PHP Code
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class runPhp
{
    /**
     * Configuration file for PHP-CS-FIXER
     *
     * @var string
     */
    private const PHP_CS_FIXER = '/var/www/html/.config/.php-cs-fixer.php';

    /**
     * Configuration file for PHPCBF
     *
     * @var string
     */
    private const PHPCBF = '/var/www/html/.config/phpcs.xml';

    /**
     * Configuration file for Rector
     *
     * @var string
     */
    private const RECTOR = '/var/www/html/.config/rector-safe.php';

    /**
     * Name of the file with the PHP code inserted by the user
     *
     * @var string
     */
    private $tmpFileName = '';

    /**
     * PHP code inserted by the user
     *
     * @var string
     */
    private $sourceCode = '';

    /**
     * Create the temporary file where we'll put the code inserted by the user
     * in the editor
     */
    public function __construct()
    {
        // This file is created in the Dockerfile so we're sure the file exists
        // and has the correct permissions
        $this->tmpFileName = '/tmp/runPhp_code.php';

        if (!is_file($this->tmpFileName)) {
            // Just in case...
            touch($this->tmpFileName);
            chmod($this->tmpFileName, 0777);
        }
    }

    /**
     * Output the code to a file in the /tmp folder then beautify it
     *
     * @return void
     */
    private function createSourceFile(): void
    {
        file_put_contents($this->tmpFileName, $this->sourceCode . PHP_EOL);
    }

    /**
     * Run php-cs-fixer.
     * php-cs-fixer.phar has been installed in the Docker image; see the `Dockerfile`
     *
     * @return void
     */
    private function runPhpCsFixer(): void
    {
        exec('php-cs-fixer.phar fix --using-cache no --config='.self::PHP_CS_FIXER.
            ' '.$this->tmpFileName.' &>/dev/null');
    }

    /**
     * Run phpcbf
     * phpcbf.phar has been installed in the Docker image; see the `Dockerfile`
     *
     * @return void
     */
    private function runPhpCbf(): void
    {
        exec('phpcbf.phar --standard='.self::PHPCBF.' '.$this->tmpFileName.' &>/dev/null');
    }

    /**
     * Run Rector
     * /vendor/bin/rector has been installed in the Docker image; see the `Dockerfile`
     *
     * @return void
     */
    private function runRector(): void
    {
        exec('vendor/bin/rector process '.$this->tmpFileName.' --config '.self::RECTOR.' &>/dev/null');
    }

    /**
     * Entry point, run the code and/or refactor it
     *
     * @return void
     */
    public function run(): void
    {
        if (isset($_POST['runphp_data'])) {
            $runphp = json_decode($_POST['runphp_data'], null, 512, JSON_THROW_ON_ERROR);

            $this->sourceCode = ltrim($runphp->code);

            if ($runphp->action === 'download') {
                if (substr($runphp->filename, -4) !== '.php') {
                    $runphp->filename .= '.php';
                }
                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename='.$runphp->filename);
                echo $this->sourceCode;
                die();
            }

            if (in_array($runphp->action, ['refactor','run'])) {
                header('Expires: Mon, 16 Apr 2012 05:00:00 GMT');
                header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Cache-Control: post-check=0, pre-check=0', false);
                header('Content-Type: text/html; charset=utf-8');
                header('Pragma: no-cache');
                header('X-XSS-Protection: 0');

                if ('refactor' === $runphp->action) {
                    $this->createSourceFile();
                    $this->runPhpCbf();
                    $this->runPhpCsFixer();
                    $this->runRector();

                    $code=file_get_contents($this->tmpFileName);
                    $code=str_replace('<', '&lt;', $code);

                    printf('<pre style="font-size:12px;">%s</pre>', $code);

                    die();
                }

                ini_set('display_errors', 1);

                switch ($runphp->settings->error_reporting) {
                    case 'fatal': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
                        break;
                    case 'warning': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING);
                        break;
                    case 'deprecated': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING | E_DEPRECATED | E_USER_DEPRECATED);
                        break;
                    case 'notice': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_NOTICE);
                        break;
                    case 'all': error_reporting(-1);
                        break;
                    case 'none': default: error_reporting(0);
                        break;
                }

                ob_start();
                eval('?>'.$this->sourceCode);
                $runphp->html = ob_get_clean();

                if ($runphp->settings->pre_wrap ?? true) {
                    $runphp->html = '<pre>'.str_replace('<', '&lt;', $runphp->html).'</pre>';
                }

                if ($runphp->settings->colorize ?? true) {
                    $runphp->html = '
                 <style>
                 html { width: 100%; background-color: '.$runphp->bgcolor.';   color: '.$runphp->color.'; }
                 .xdebug-error th { background-color: #'.$runphp->bgcolor.'; font-weight: normal; font-family: sans-serif; }
                 .xdebug-error td { color: '.$runphp->color.'; }
                 .xdebug-error th span { background-color: '.$runphp->bgcolor.' !important; }
                 </style>'.$runphp->html;
                }
                echo $runphp->html;
                die();
            }
        } else {
            header('Content-Type: text/html; charset=utf-8');
        }
    }
}

$runPhp = new runPhp();
$runPhp->run();

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Run PHP Code</title>
      <link rel="shortcut icon" href="favicon.ico" >
      <link rel="stylesheet" href="css/font-awesome.min.css">
      <link rel="stylesheet" href="css/run_php_code.css">
   </head>
   <body>
      <form id="runphp_form" method="POST" action="" target="result_frame" data-bind="attr: { target: settings.run_external() ? 'result_external' : 'result_frame' }">
         <input type="hidden" name="runphp_data" value="" />
      </form>

      <div id="title_bar">
         <div id="title">Run PHP Code</div>
         <div class="drop">
            <span>File</span>
            <div>
               <div class="clickable"><a data-bind="click: php_info">phpinfo()</a></div>
               <div class="clickable"><a data-bind="click: download_file">Download...</a></div>
            </div>
         </div>
         <div class="drop"><span>Options</span>
            <div>
               <div class="checkbox" data-bind="my_checkbox: settings.colorize, click: change_setting" data-label="Colorize"></div>
               <div class="checkbox" data-bind="my_checkbox: settings.run_external, click: change_setting" data-label="External Window"></div>
               <div class="checkbox" data-bind="my_checkbox: settings.pre_wrap, click: change_setting" data-label="&lt;pre&gt; Wrap"></div>
               <div class="subdrop">
                  Error Reporting
                  <div>
                     <div class="radio" data-bind="my_radio: settings.error_reporting" data-value="none" data-label="None"></div>
                     <div class="radio" data-bind="my_radio: settings.error_reporting" data-value="fatal" data-label="Fatal"></div>
                     <div class="radio" data-bind="my_radio: settings.error_reporting" data-value="warning" data-label="Warning"></div>
                     <div class="radio" data-bind="my_radio: settings.error_reporting" data-value="deprecated" data-label="Deprecated"></div>
                     <div class="radio" data-bind="my_radio: settings.error_reporting" data-value="notice" data-label="Notice"></div>
                     <div class="radio" data-bind="my_radio: settings.error_reporting" data-value="all" data-label="All"></div>
                  </div>
               </div>
            </div>
         </div>
         <div class="drop"><span>Themes</span>
            <div>
               <div class="subdrop">
                  Light
                  <div>
                     <!-- ko foreach: themes.light -->
                        <div class="checkbox" data-bind="attr: { 'data-value': theme, 'data-label': title }, my_radio: $parent.settings.theme, click: $parent.change_setting"></div>
                     <!-- /ko -->
                  </div>
               </div>
               <div class="subdrop">
                  Dark
                  <div>
                     <!-- ko foreach: themes.dark -->
                        <div class="checkbox" data-bind="attr: { 'data-value': theme, 'data-label': title }, my_radio: $parent.settings.theme, click: $parent.change_setting"></div>
                     <!-- /ko -->
                  </div>
               </div>
            </div>
         </div>
         <div class="drop drop_help_window" data-bind="event: { mouseover: load_contributors }">
            <span><i class="fa fa-question"></i></span>
            <div id="help_window">
               <div style="padding: 10px;">
                  <h2>Run PHP Code</h2>

                  <p>
                     <img src="img/website_duck.png" alt="" style="width: 40px; height: 40px;"><br>
                     &copy; Website Duck LLC<br />
                  </p>

                  <a class="button" href="https://github.com/websiteduck/Run-PHP-Code"><i class="fa fa-github"></i> GitHub Repo</a><br>
               </div>

               <div class="subdrop with_icon" style="text-align: left;">
                  <i class="fa fa-users"></i> Contributors
                  <div>
                     <ul data-bind="foreach: contributors" id="contributors">
                        <li>
                           <label><a data-bind="attr: { href: url }"><img data-bind="attr: { src: avatar_url + '&s=24' }" /> <span data-bind="text: login"></span></a></label>
                        </li>
                     </ul>
                  </div>
               </div>
               <div class="subdrop with_icon" style="text-align: left;">
                  <i class="fa fa-heart"></i> Attributions
                  <div>
                     <ul>
                        <li><label><a href="http://ace.ajax.org"> Ace</a></label></li>
                        <li><label><a href="http://fortawesome.github.io/Font-Awesome"> Font Awesome</a></label></li>
                        <li><label><a href="http://jquery.com"> jQuery</a></label></li>
                        <li><label><a href="http://knockoutjs.com"> Knockout</a></label></li>
                     </ul>
                  </div>
               </div>

            </div>
         </div>

         <div id="button_container">
            <button class="button" type="button" data-bind="click: clear"><i class="fa fa-eraser"></i> &nbsp; Clear</button>
            <button class="button" type="button" title="Run (Ctrl+Enter)" data-bind="click: run">Run &nbsp; <i class="fa fa-play"></i></button>
            <button class="button" type="button" title="Refactor the code with php-cs-fixer and rector" data-bind="click: refactor"><i class="fa fa-edit"></i> &nbsp; Refactor</button>
         </div>
      </div>

      <div id="code_div" data-bind="style: { width: code_width() + 'px' }"></div>
      <div id="result_div" data-bind="visible: !settings.run_external(), style: { width: result_width() + 'px' }"><iframe id="result_frame" name="result_frame" data-bind="event: { load: result_loaded }"></iframe></div>
      <div id="resize_bar" data-bind="visible: !settings.run_external(), style: { left: settings.divide_x() + 'px' }"></div>

      <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
      <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
      <script type="text/javascript" src="js/ace/ace.js" charset="utf-8"></script>
      <script type="text/javascript" src="js/knockout-3.5.1.js"></script>
      <script type="text/javascript" src="js/run_php_code.js"></script>
   </body>
</html>
