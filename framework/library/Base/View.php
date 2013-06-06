<?php
/**
 * 模板基类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: View.php 560 2013-05-17 07:01:02Z 85825770@qq.com $
 */
abstract class Base_View {

	/**
	 * 模板后缀
	 *
	 * @var string
	 */
	public $_ext = '.html';

	/**
	 * 模版所在目录
	 *
	 * @var string
	 */
	public $view_dir = null;
	public $compile_dir = null;

	public $_referesh = true;

	/**
	 * 模板缓存文件
	 *
	 * @var string
	 */
	public $compilefile;

	public function __construct() {
		$this->view_dir = WEKIT_PATH . 'template' . DIRECTORY_SEPARATOR;
		$this->compile_dir = DATA_PATH . 'template' . DIRECTORY_SEPARATOR;
		$this->init();
	}

	/**
	 * 初始化模板引擎
	 */
	abstract public function init();

	/**
	 * 缓存重写分析
	 *
	 * @param string $template
	 * @param string $application
	 * @param string $style
	 */
	public function compile($template, $application = null, $style = 'default') {
		$application = (is_null ( $application ) ? APP : $application);
		$this->style = $style;
		// 定义模版路径
		$tplfile = $this->view_dir . $style . DIRECTORY_SEPARATOR . $application . DIRECTORY_SEPARATOR . $template . $this->_ext;
		if (! file_exists ( $tplfile )) {
			throw new Exception ( 'Unable to load the file ' . $tplfile . ' , file is not exist.' );
		}
		$filepath = $this->compile_dir . $style . DIRECTORY_SEPARATOR . $application . DIRECTORY_SEPARATOR;
		if (! is_dir ( $filepath )) Folder::mk($filepath);
		$this->compilefile = $filepath . $template . '.php';
		if (! file_exists ( $this->compilefile ) || ($this->_referesh && (@filemtime ( $tplfile ) > @filemtime ( $this->compilefile )))) {
			$this->refresh ( $tplfile);
		}
		return $this->compilefile;
	}

	/**
	 * 生成视图缓存
	 *
	 * @param string $tplfile
	 * @return number
	 */
	public function refresh($tplfile) {
		$str = Utils_File::read ( $tplfile );
		$str = $this->parse ( $str );
		$strlen = Utils_File::write($this->compilefile, $str);
		return $strlen;
	}

	/**
	 * 解析视图
	 *
	 * @param string $str
	 * @return ture
	 */
	public function parse($str) {
		/*
		 * 去掉此处注释模板将不支持PHP原生语法 $find[] = '/\<\?.*?\?>/'; $replace[] = '';
		 */
		// 过滤模版中的注释
		$find [] = "/\<(!--)\*\*(\S+?)\*\*-->/";
		$replace [] = '';
		// 模版中通过V函数加载模版
		$find [] = "/\{V\s+(.+)\}/";
		$replace [] = "<?php include V(\\1); ?>";
		// 引入文件
		$find [] = "/\{include\s+(.+)\}/";
		$replace [] = "<?php include \\1; ?>";

		// 加载语言包
		$find [] = "/\{lang\s+(\S+)\}/ies";
		$replace [] = "L('\\1')";
		$find [] = "/\{lang\s+(\S+)\s+(\S+)\}/ies";
		$replace [] = "L('\\1','','\\2')";

		// 模版内PHP语法
		$find [] = "/\{php\s+(.+)\}/";
		$replace [] = "<?php \\1?>";
		// 流程控制
		$find [] = "/\{if\s+(.+?)\}/";
		$replace [] = "<?php if(\\1) { ?>";
		$find [] = "/\{else\}/";
		$replace [] = "<?php } else { ?>";
		$find [] = "/\{elseif\s+(.+?)\}/";
		$replace [] = "<?php } elseif (\\1) { ?>";
		// for 循环
		$find [] = "/\{for\s+(.+?)\}/";
		$replace [] = "<?php for(\\1) { ?>";
		// ++ --
		$find [] = "/\{\+\+(.+?)\}/";
		$replace [] = "<?php ++\\1; ?>";
		$find [] = "/\{\-\-(.+?)\}/";
		$replace [] = "<?php --\\1; ?>";
		$find [] = "/\{(.+?)\+\+\}/";
		$replace [] = "<?php \\1++; ?>";
		$find [] = "/\{(.+?)\-\-\}/";
		$replace [] = "<?php \\1--; ?>";
		$find [] = "/\{loop\s+(\S+)\s+(\S+)\}/";
		$replace [] = "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>";
		$find [] = "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/";
		$replace [] = "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>";
		$find [] = "/\{\/loop\}/";
		$replace [] = "<?php if(isset(\$n))\$n++;} if(isset(\$n))unset(\$n); ?>";
		$find [] = "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/";
		$replace [] = "<?php echo  \\1 ;?>";
		$find [] = "/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/";
		$replace [] = "<?php echo  \\1 ;?>";
		// 解析变量
		$find [] = "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/";
		$replace [] = "<?php echo  isset(\\1) ? \\1 : '';?>";
		$find [] = "/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es";
		$replace [] = "\$this->addquote('<?php echo \\1;?>')";
		// 解析常量
		$find [] = "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s";
		$replace [] = "<?php echo defined('\\1') ? \\1 : '';?>";

		/**
		 $find [] = "/\<\/body\>/";
		 $replace [] = "<?php/**
		 * $find [] = "/\<\/body\>/";
		 * $replace [] = "<?php echo show_time(); ?>\r\n</body>";
		 */


		$find [] = "/\<\/body\>/";
		$replace [] = "\r\n<?php echo show_trace(); ?>\r\n</body>";
		$find [] = "/\<\/body\>/";
		$replace [] = "<?php echo base64_decode('PCEtLUxlYXBzIFBIUCBGcmFtZXdvcmsg5piv5LiA5Liq5oCn6IO95Y2T6LaK5bm25LiU5Yqf6IO95Liw5a+M55qE6L276YeP57qnUEhQ5byA5Y+R5qGG5p6277yM5a6X5peo5bCx5piv6K6pV0VC5bqU55So5byA5Y+R5pu0566A5Y2V44CB5pu05b+r6YCf44CCTGVhcHMgUEhQIEZyYW1ld29ya+eahOWumOaWuee9keermeaYr2h0dHA6Ly93d3cudGludHNvZnQuY29tIC0tPg==');?>\r\n</body>";
		// 更多语法请您自由添加 ^_^

		// 编译文件结构调整
		$str = preg_replace ( $find, $replace, $str );
		$find = array ("/\{\/if\}/","/\{\/for\}/" );
		$str = preg_replace ( $find, "<?php } ?>", $str );
		return $str;
	}

	/**
	 * 转义 // 为 /
	 *
	 * @param string $var
	 * @return 转义后的字符
	 */
	public function addquote($var) {
		return str_replace ( "\\\"", "\"", preg_replace ( "/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var ) );
	}
}