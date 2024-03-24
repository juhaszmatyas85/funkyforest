<?php
namespace Dwp\Blocks\BlockStyle\Pinterest;


class PinterestStyleOne
{
    // Pinterest Style One Editor Style
    public function flexguten_frontend_styles( $attr, $handle ){
    
        $css = '';
    
        if(!empty($attr['backgroundColor'])){
            $css .= ".pinit-container.$handle{";            
                $css .= "background-color:{$attr['backgroundColor']};";    
            $css .= "}";
        }

        if($attr['containerBorder']['width'] !== "0px"){
            $css .= ".pinit-container.$handle{";
                $css .= "border:{$attr['containerBorder']['width']} {$attr['containerBorder']['style']} {$attr['containerBorder']['color']};";
                $css .= "border-radius:{$attr['containerBorderRadius']}px;";
            $css .= "}";
        }

        /**
         * Title Styles
         */
            $css .= ".$handle .flexguten-pinit-card-title h4{";  
                $CMTop = $attr['cardTitleMargin']['top'] ? $attr['cardTitleMargin']['top'] : 0;          
                $CMRight = $attr['cardTitleMargin']['right'] ? $attr['cardTitleMargin']['right'] : 0;          
                $CMBottom = $attr['cardTitleMargin']['bottom'] ? $attr['cardTitleMargin']['bottom'] : 0;          
                $CMLeft = $attr['cardTitleMargin']['left'] ? $attr['cardTitleMargin']['left'] : 0;      
                //var_dump( $CMTop );    
                $css .= "color:{$attr['titleColor']};";  
                $css .= "margin:{$CMTop}px {$CMRight}px {$CMBottom}px {$CMLeft}px;";  
                $css .= "font-size:{$attr['cardTitleFontSize']['desktop']}px;";  
            $css .= "}";

         /**
         * Sub Title Styles
         */
            $css .= ".$handle .flexguten-pinit-card-subtitle{";            
                $CMSTop = $attr['cardSubTitleMargin']['top'] ? $attr['cardSubTitleMargin']['top'] : 0;          
                $CMSRight = $attr['cardSubTitleMargin']['right'] ? $attr['cardSubTitleMargin']['right'] : 0;          
                $CMSBottom = $attr['cardSubTitleMargin']['bottom'] ? $attr['cardSubTitleMargin']['bottom'] : 0;          
                $CMSLeft = $attr['cardSubTitleMargin']['left'] ? $attr['cardSubTitleMargin']['left'] : 0;          
                $css .= "color:{$attr['subTitleColor']};";  
                $css .= "margin:{$CMSTop}px {$CMSRight}px {$CMSBottom}px {$CMSLeft}px;";  
                $css .= "font-size:{$attr['cardSubTitleFontSize']['desktop']}px;";
            $css .= "}";
        
        /**
         * Button Styles
         */
        

        $css .= ".$handle .pinterest-button{";            
            $CMBTop = ['top'] ? $attr['buttonMargin']['top'] : 0;          
            $CMBRight = $attr['buttonMargin']['right'] ? $attr['buttonMargin']['right'] : 0;          
            $CMBBottom = $attr['buttonMargin']['bottom'] ? $attr['buttonMargin']['bottom'] : 0;          
            $CMBLeft = $attr['buttonMargin']['left'] ? $attr['buttonMargin']['left'] : 0;          
            $css .= "margin:{$CMBTop}px {$CMBRight}px {$CMBBottom}px {$CMBLeft}px;";  
            $css .= "background-color:{$attr['buttonBackgroundColor']};"; 
            $css .= "color:{$attr['buttonTextColor']};";             
        $css .= "}";
        $css .= ".$handle .pinterest-button svg{"; 
            $css .= "fill:{$attr['buttonTextColor']};"; 
        $css .= "}"; 

        /**
         * Image Styles
         */
        $css .= ".$handle .review-image img{";            
            $css .= "height:{$attr['imageHeight']}px;";  
            $css .= "width:{$attr['imageWidth']}%;";  
        $css .= "}";
        
        return $css;
    }

    // Pinterest Style One Frontend Render

    public function flexguten_frontend_render( $attr, $id ){        
        $p = get_post();

			if( ! empty( $p ) ){
				
					$title = $p->post_title ? $p->post_title : 'Default title';
					$url = esc_url( get_permalink( $p->ID ) );


					$thumbnail = !empty($attr['photo']['url']) ? $attr['photo']['url'] : '';
					$card_title = $attr['cardTitle'] ? $attr['cardTitle'] : '';
					$card_sub_title = $attr['cardSubTitle'] ? $attr['cardSubTitle'] : '';
					$button_label = $attr['buttonLabel'] ? $attr['buttonLabel'] : '';
					$button_icon = '<svg
					clip-rule="evenodd"
					class="icon-class"
					fill-rule="evenodd"
					stroke-linejoin="round"
					stroke-miterlimit="2"
					viewBox="0 0 24 24"
				>
					<path
						d="m14.523 18.787s4.501-4.505 6.255-6.26c.146-.146.219-.338.219-.53s-.073-.383-.219-.53c-1.753-1.754-6.255-6.258-6.255-6.258-.144-.145-.334-.217-.524-.217-.193 0-.385.074-.532.221-.293.292-.295.766-.004 1.056l4.978 4.978h-14.692c-.414 0-.75.336-.75.75s.336.75.75.75h14.692l-4.979 4.979c-.289.289-.286.762.006 1.054.148.148.341.222.533.222.19 0 .378-.072.522-.215z"
						fill-rule="nonzero"
					/>
				</svg>';			
					
					$output = '';
					$output .= '<div class="pinit-container '.$id.' " >';
					$output .= '<div class="pinit-left"><div class="vertical">';
					$output .= '<div class="flexguten-pinit-card-title"><h4>'.$card_title.'</h4></div>';
					$output .= '<div class="flexguten-pinit-card-subtitle">'.$card_sub_title.'</div>';
					$output .= '<a class="pinterest-button-style" href="https://www.pinterest.com/pin/create/button/" data-pin-media="'.$thumbnail.'" data-pin-url="'.$url.'" data-pin-do="buttonPin" data-pin-description="Custom Data Pin Desc" data-pin-custom="true"><div class="pinterest-button desktop-button ">'.$button_label. ' '.$button_icon.'</div></a>';
					$output .= '</div></div>';
					$output .= '<div class="pinit-right">';
                    if($thumbnail !== ''){
					    $output .= '<div class="review-image"><img src="'.$thumbnail.'"></div>';	
                    }			
					$output .= '</div>';				
					$output .= '</div>';

                    return $output ?? '<strong>Sorry. No Post Matches!</strong>';
				
			}
			
    }
}
