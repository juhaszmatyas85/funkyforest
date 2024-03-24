<?php
namespace Dwp\Blocks\BlockStyle\PostGridOne;
use Dwp;

class PostGridOne
{

    public $asset;    

    function __construct(){
        add_action( 'init', [ $this, 'flexguten_blocks_init' ] );
        $this->asset = new Dwp\Assets();
        }
       

    // Post Grid One Editor Style
    public function flexguten_frontend_styles( $attributes, $handle ){ 
      
            $handle = $attributes['id'];

            $css ='';
          
            $css .= ".$handle.post-grid-main{";
              if(!empty($attributes['containerBg'])) {
                $css .= "background-color: {$attributes['containerBg']};";               
              }
              $css .= "grid-template-columns: repeat({$attributes['numberofRows']}, 1fr);";
              $css .= "padding: {$attributes['conatainerPadding']}px"; 
            $css .= "}";
          
            
            $css .= ".$handle .post-single-item .content-section{";
                  if(!empty($attributes['contentBg'])) {
                  $css .= "background-color: {$attributes['contentBg']};";
                  }
              $css .= "}";
              
              $css .= ".$handle .post-title h4 a{";
                  if(!empty($attributes['headingColor'])) {
                      $css .= "color: {$attributes['headingColor']}!important;";
                  }    
              $css .= "}";
              
              $css .= ".$handle .post-excerpt{";
                  if(!empty($attributes['excerptColor'])) {
                      $css .= "color: {$attributes['excerptColor']};";
                  }    
              $css .= "}";
              
              $css .= ".$handle .content-hyperlink a{";
                  if(!empty($attributes['readMoreColor'])) {
                      $css .= "color: {$attributes['readMoreColor']}!important;";
                  }    
              $css .= "}";
              
           
          
          
            // Desktop
            $css .= "@media (min-width: 1025px) {";
          
              $css .= ".$handle .post-title h4 a{";
                $css .= "font-size: {$attributes['headingFontSizes']['desktop']}px;";
              $css .= "}";
              
              $css .= ".$handle .post-excerpt{";
                $css .= "font-size: {$attributes['excerptFontSizes']['desktop']}px;";
              $css .= "}";
              
              $css .= ".$handle .content-hyperlink a{";
                $css .= "font-size: {$attributes['readMoreFontSizes']['desktop']}px;";
              $css .= "}";
          
            $css .= "}";
          
            // Tablet
            $css .= "@media (min-width: 768px) and (max-width: 1024px) {";
          
                $css .= ".$handle .post-title h4 a{";
                  $css .= "font-size: {$attributes['headingFontSizes']['tablet']}px;";
                $css .= "}";
                
                $css .= ".$handle .post-excerpt{";
                  $css .= "font-size: {$attributes['excerptFontSizes']['tablet']}px;";
                $css .= "}";
                
                $css .= ".$handle .content-hyperlink a{";
                  $css .= "font-size: {$attributes['readMoreFontSizes']['tablet']}px;";
                $css .= "}";
          
            // Mobile
            $css .= "@media (max-width: 767px) {";
              
              $css .= ".$handle .post-title h4 a{";
                  $css .= "font-size: {$attributes['headingFontSizes']['mobile']}px;";
                $css .= "}";
                
                $css .= ".$handle .post-excerpt{";
                  $css .= "font-size: {$attributes['excerptFontSizes']['mobile']}px;";
                $css .= "}";
                
                $css .= ".$handle .content-hyperlink a{";
                  $css .= "font-size: {$attributes['readMoreFontSizes']['mobile']}px;";
                $css .= "}";
          
            $css .= "}";
          
             return $css; 
           

    }

    public function flexguten_frontend_render( $attr, $id ){  
        
        
		$number_of_posts = isset($attr['numberOfPosts']) ? $attr['numberOfPosts'] : -1;
		$categories = isset($attr['categories']) ? $attr['categories'] : [];
		$post_filter_type = isset($attr['postFilter']) ? $attr['postFilter'] : 'latest';
		$posts = isset($attr['posts']) ? $attr['posts'] : [];

		$category_ids = [];

		foreach ($categories as $category) {
			$category_ids[] = $category['value'];
		}

		$post_ids = [];

		foreach ($posts as $post) {
			$post_ids[] = $post['value'];
		}

		

		$args = [
			'post_type' => 'post',
		];

		if( $post_filter_type !== 'individual'){
			$args['posts_per_page'] = $number_of_posts;
		}

		if( $post_filter_type == 'category'){
			$args['category__in'] = $category_ids;
		}

		if( $post_filter_type == 'individual'){
			$args['post__in'] = $post_ids;
		}

		
	
		$post_query = new \WP_Query($args);

		$content = '';
		$content .= '<div class=" '.$id.' post-grid-main">';
		
		if($post_query->have_posts() ) {
			while($post_query->have_posts() ) {
				$post_query->the_post();
				

				$image = get_the_post_thumbnail();
				
				$content .= '<div class="post-single-item">';
				$content .= '<div class="header_section">';
				$content .= '<div class="featured-image">';
				$content .= $image;
				$content .= '<div class="categories"><div class="category-item">'.get_the_category_list().'</div></div>';
				$content .= '</div></div>';
				$content .= '<div class="content-section">';
				$content .= '<div class="post-title"><h4><a href="'. get_the_permalink().'">'. get_the_title().'</a></h4></div>';
				$content .= '<div class="post-excerpt">'.get_the_excerpt().'</div>';
				$content .= '<div class="content-hyperlink"><a href="'.get_the_permalink().'">Continue Reading &gt;&gt;</a></div>';
				$content .= '</div></div>';			
					
				}
			}

			$content .= '</div>';
		return $content;	
			
    }

}
