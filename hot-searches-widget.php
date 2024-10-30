<?php
class HotSearchesWidget
{
	function HotSearchesWidget()
	{
		add_action('widgets_init', array($this, 'init'));
	}

	function init()
	{
		register_sidebar_widget('Hot Searches ', array($this, 'hsWidget'));
		register_widget_control('Hot Searches ', array($this, 'widgetControl'), 200, 400);
	}

	function hsWidget($args)
	{
		extract($args);

		# Get the options
		$options = get_option('hot_searches_widget');
		if ( !is_array($options) ) {
			$options = array('title'=>'Hot Searches', 'count'=>10, 'showcount'=>true,'showauthor'=>true,'searchtype'=>0);
		}
		# Before the widget
		echo $before_widget;

		# The title
		echo $before_title . $options['title'] . $after_title;

		# Make the stats chicklet
		$keys = get_keys();
                echo "<ul>";
                $count = $options['count'];
                $idx = 0;
                $siteurl = get_bloginfo( 'siteurl');

                if(is_array($keys))
                {
                    foreach($keys as $keyname=>$keycount)
                    {
                        $idx++;
                        if($idx>=$count)
                            break;
                        if($options['searchtype']==0)
                            $href = $siteurl.'/?s='.$keyname;
                        else
                            $href = '#';
                        $keyname = urldecode($keyname);
                        if($options['showcount'])
                        {
                                echo "<li class=\"googlesearch\"><input type=\"hidden\" value=\"$keyname\"><a href=\"$href\">$keyname(".$keycount.")</a></li>";
                        }
                        else
                        {
                            echo "<li class=\"googlesearch\"><input type=\"hidden\" value=\"$keyname\"><a href=\"$href\">$keyname</a></li>";
                        }
                    }
                }


		# After the widget
                if($options['showauthor'])
                    echo "<li><a href=\"http://miscgarden.com\"> Powered by Miscgarden </a>";
                echo "</ul>";
		echo $after_widget;
	}

	/**
	 * The settings for the stats widget
	 **/
	function widgetControl()
	{
		# Get the widget options
		$options = get_option('hot_searches_widget');
		if ( !is_array($options) ) {
			$options = array('title'=>'Hot Searches', 'count'=>10, 'showcount'=>true,'showauthor'=>true,'searchtype'=>0);
		}
		# Save the options

		if ( $_POST['hot-searches-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['hot-searches-title']));
			$options['count'] = strip_tags(stripslashes($_POST['hot-searches-account']));
                        if(empty($_POST['hot-searches-showcount']))
                            $options['showcount'] = false;
                        else
                            $options['showcount'] = true;
                        if(empty($_POST['hot-searches-showauthor']))
                            $options['showauthor'] = false;
                        else
                            $options['showauthor'] = true;
                        $options['searchtype']=(int)stripslashes($_POST['hot-searches-searchtype']);
			update_option('hot_searches_widget', $options);
		}

		# Sanitize widget options
		$title = htmlspecialchars($options['title']);
		$count = $options['count'];
		$showcount = $options['showcount'];
                $showauthor =  $options['showauthor'];
                $searchtype = (int)$options['searchtype'];

                if($showcount)
                    $cntchecked = 'checked';
                else
                    $cntchecked = '';

                if($showauthor)
                    $authchecked = 'checked';
                else
                    $authchecked = '';

		# Output the options
		echo '<p><label for="hot-searches-title">' . __('Title:') . '<br/><input id="hot-searches-title" name="hot-searches-title" type="text" value="' . $title . '" /></label></p>';
		echo '<p><label for="hot-searches-account">' . __('Number of keys to show :') . '<br/><input  id="hot-searches-account" name="hot-searches-account" type="text" value="' . $count . '" /></label></p>';
		echo '<p><label for="hot-searches-searchtype">' . __('Search Type:') ;
                echo ' <select  id="hot-searches-searchtype" name="hot-searches-searchtype" >';
                if($searchtype == 1)
                    echo '<option value="0" >Wordpress Intergrated Search</option><option value="1" selected>Google Adsense Search</option>';
                else
                    echo '<option value="0" selected>Wordpress Intergrated Search</option><option value="1">Google Adsense Search</option>';
                echo '</select></label></p>';
                echo '<p><label for="hot-searches-showcount">' . __('Show count of searches:') . ' <input  id="hot-searches-showcount" name="hot-searches-showcount" type="checkbox" ' . $cntchecked . ' /></label></p>';
		echo '<p><label for="hot-searches-showauthor">' . __('Show author website:') . ' <input  id="hot-searches-showauthor" name="hot-searches-showauthor" type="checkbox" ' . $authchecked . ' /></label></p>';
		echo '<input type="hidden" id="hot-searches-submit" name="hot-searches-submit" value="1" />';
                echo '<a href = "http://miscgarden.com/hs" target="_blank">donate</a>';
	}



} // END class

?>