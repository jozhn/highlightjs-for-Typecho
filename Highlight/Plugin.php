<?php
/**
 * Highlight插件，代码高亮，github风格
 * 
 * @package Highlight.js
 * @author Jozhn
 * @version 1.0
 * @link https://dearjohn.cn
 */
class Highlight_Plugin implements Typecho_Plugin_Interface
{
    /**
     *   
     */
    private static $_isMarkdown = false;

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('Highlight_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('Highlight_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('Highlight_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Archive')->header = array('Highlight_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Highlight_Plugin', 'footer');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
      echo "<center><h1>注意：改插件需要模板自行加载jquery.js</h1></center>";
        $compatibilityMode = new Typecho_Widget_Helper_Form_Element_Radio('compatibilityMode', array(
            0   =>  _t('不启用'),
            1   =>  _t('启用')
        ), 0, _t('兼容模式'), _t('兼容模式一般用于对以前没有使用Markdown语法解析的文章'));
        $form->addInput($compatibilityMode->addRule('enum', _t('必须选择一个模式'), array(0, 1)));
		
		$styleUrl = new Typecho_Widget_Helper_Form_Element_Text('styleUrl', NULL, 'styleUrl',
             _t('CSS URL'), _t('请填写CSS URL如:https://cdn.bootcss.com/highlight.js/9.12.0/styles/github.min.css'));
        $form->addInput($styleUrl->addRule('required', _t('必须填写一个CSS URL')));
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 输出头部css
     * 
     * @access public
     * @param unknown $header
     * @return unknown
     */
    public static function header() {
        $cssUrl = Helper::options()->plugin('Highlight')->styleUrl;
         echo '<link rel="stylesheet" type="text/css" href="' . $cssUrl . '" />';
    }
    
    /**
     * 输出尾部js
     * 
     * @access public
     * @param unknown $header
     * @return unknown
     */
    public static function footer() {
        echo '<script type="text/javascript" src="https://cdn.bootcss.com/highlight.js/9.12.0/highlight.min.js"></script>';
        echo '<script type="text/javascript">$("pre code").each(function(i, block) {hljs.highlightBlock(block);});';
        echo '</script>';
    }
    
    /**
     * 解析
     * 
     * @access public
     * @param array $matches 解析值
     * @return string
     */
    public static function parseCallback($matches)
    {
        if ('code' == $matches[1] && !self::$_isMarkdown) {
            $language = $matches[2];

            if (!empty($language)) {
                if (preg_match("/^\s*(class|lang|language)=\"(?:lang-)?([_a-z0-9-]+)\"$/i", $language, $out)) {
                    $language = ' class="' . trim($out[2]) . '"';
                } else if (preg_match("/\s*([_a-z0-9]+)/i", $language, $out)) {
                    $language = ' class="lang-' . trim($out[1]) . '"';
                }
            }
            
            return "<pre><code{$language}>" . htmlspecialchars(trim($matches[3])) . "</code></pre>";
        }

        return $matches[0];
    }
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function parse($text, $widget, $lastResult)
    {
        $text = empty($lastResult) ? $text : $lastResult;

        if (!Helper::options()->plugin('Highlight')->compatibilityMode) {
            return $text;
        }
        
        if ($widget instanceof Widget_Archive || $widget instanceof Widget_Abstract_Comments) {
            self::$_isMarkdown = $widget instanceof Widget_Abstract_Comments ? Helper::options()->commentsMarkdown : $widget->isMarkdown;
            return preg_replace_callback("/<(code|pre)(\s*[^>]*)>(.*?)<\/\\1>/is", array('Highlight_Plugin', 'parseCallback'), $text);
        } else {
            return $text;
        }
    }
}
