<?php
class Smallgallery_Walker_Comment extends Walker_Comment {
	 
	var $tree_type = 'comment';
	var $db_fields = array(
		'parent' => 'comment_parent',
		'id' => 'comment_ID'
	);
 
	function __construct() {
		global $post;
		$comments_title = sprintf(__('Comments on <strong>%s</strong>',TEXTDOMAIN),$post->filtered_title);

		echo <<<CONSTRUCT

		<h3 id="comments-title">{$comments_title}</h3>
		<ol id="comments" class="comments">

CONSTRUCT;
	}
	 
	function start_lvl(&$output,$depth=0,$args=array()){	  
		$GLOBALS['comment_depth'] = $depth + 1;
		echo <<<START_LVL

				<ol class="children">

START_LVL;
	}
 
	function end_lvl(&$output,$depth=0,$args=array()){
		$GLOBALS['comment_depth'] = $depth + 1;
		echo <<<END_LVL

		</ul><!-- /.children -->

END_LVL;
	}
	 
	function start_el(&$output,$comment,$depth,$args,$id=0){
		global $post;
		$depth++;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment'] = $comment;
		$parent_class = (empty($args['has_children'])?'':'parent');

		if(get_option('thread_comments')){
			$replyOptions = array_merge($args,array(
				'add_below' => 'comment-body',
				'depth' => $depth,
				'max_depth' => $args['max_depth']
			));
			ob_start();
			comment_reply_link($replyOptions,$comment,$post);
			$comment->reply_link = ob_get_contents();
			ob_end_clean();
		}else{
			$comment->reply_link = '';
		}

		ob_start();
		edit_comment_link(__('Edit',TEXTDOMAIN));
		$comment->edit_link = ob_get_contents();
		ob_end_clean();

		$comment->says = __(' says,',TEXTDOMAIN);
		$comment->at = __(' at ',TEXTDOMAIN);
		$comment->permalink = htmlspecialchars(get_comment_link());
		$comment->class = comment_class($parent_class,null,null,false);
		$comment->portrait = $args['avatar_size']!=0?get_avatar($comment,$args['avatar_size']):'';
		$comment->filtered_author = get_comment_author_link();
		$comment->filtered_date = get_comment_time(get_option('date_format'))."<span class='at'>{$comment->at}</span>".get_comment_time(get_option('time_format'));

		if(!$comment->comment_approved){
			$comment->filtered_text = __('Your comment is awaiting moderation.',TEXTDOMAIN);
		}else{
			$comment->filtered_text = get_comment_text();
		}
		$comment->filtered_text = apply_filters('comment_text',$comment->filtered_text);

		echo <<<START_EL
		 
		<li id="comment-{$comment->comment_ID}" {$comment->class}>
			<div id="comment-body-{$comment->comment_ID}" class="comment-body">
				<div class="comment-author vcard author">
					{$comment->portrait}
					<cite class="fn n author-name">{$comment->filtered_author}<span class="says">{$comment->says}</span></cite>
				</div><!--/.comment-author-->
				<div id="comment-content-{$comment->comment_ID}" class="comment-content">
					{$comment->filtered_text}
				</div><!--/.comment-content-->
				<div class="comment-meta comment-meta-data">
					<span class="comment-date">{$comment->filtered_date}</span>
					{$comment->reply_link}
					{$comment->edit_link}
				</div><!--/.comment-meta-->
			</div><!--/.comment-body-->

START_EL;
	}
 
	function end_el(&$output,$comment,$depth=0,$args=array()){
		echo <<<END_EL

		</li><!-- /#comment-{$comment->comment_ID}-->
		 
END_EL;
	 }

	function __destruct() {
		echo <<<DESTRUCT

	</ul><!-- /#comment-list -->

DESTRUCT;
	}
}
?>
