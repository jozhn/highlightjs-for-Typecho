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
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */ 
    public static function activate()
    {
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
    public static function config(Typecho_Widget_Helper_Form $form){}
    
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
         echo '<link rel="stylesheet" type="text/css" href="https://cdn.bootcss.com/highlight.js/9.12.0/styles/github.min.css" />';
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
        echo '<script type="text/javascript">$("pre code").each(function(i, block) {hljs.highlightBlock(block);});</script>';
    }
}
