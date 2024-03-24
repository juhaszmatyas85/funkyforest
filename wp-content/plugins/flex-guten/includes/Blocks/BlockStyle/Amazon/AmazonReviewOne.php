<?php
namespace Dwp\Blocks\BlockStyle\Amazon;


class AmazonReviewOne
{
    // Amazon Review One Editor Style
    public function flexguten_frontend_styles( $attributes, $handle ){
    
    $css = '';

    if(!empty($attributes['containerBg'])){
        $css .= ".wp-block-flexguten-amazon-review-one.$handle {";            
            $css .= "background:{$attributes['containerBg']};";          
        $css .= "}";
    }

    if(!empty($attributes['containerBorder']['width'] !== "0px")){
        $css .= ".wp-block-flexguten-amazon-review-one.$handle {";
            $css .= "border:{$attributes['containerBorder']['width']} {$attributes['containerBorder']['color']} {$attributes['containerBorder']['style']};";
        $css .= "}";
    }

    if(!empty($attributes['containerBorderRadius'] !== 0)){
        $css .= ".wp-block-flexguten-amazon-review-one.$handle {";
            $css .= "border-radius:{$attributes['containerBorderRadius']}px;";
        $css .= "}";
    }

    if(!empty($attributes['imageHeightWidth'] !== 0)){
        $css .= ".review-image img {";
            $css .= "height:{$attributes['imageHeightWidth']['height']}%!important;";
            $css .= "width:{$attributes['imageHeightWidth']['width']}%!important;";            
            $css .= "}";
    }
    
    $css .= ".amazon-review-block .star-rating {";
        $css .= "color:{$attributes['starRatingColor']};";
    $css .= "}";
        
    $css .= ".rating-count p {";
        $css .= "color:{$attributes['reviewTextColor']};";
        $css .= "font-size:{$attributes['ReviewTextFontSize']}px;";
    $css .= "}";
        
    $css .= ".review-text h3 {";
        $css .= "color:{$attributes['headingFontColor']};";
        $css .= "font-size:{$attributes['headingFontSize']}px;";
    $css .= "}";
        
    $css .= ".product-features li {";
        $css .= "color:{$attributes['featuresColor']};";
        $css .= "font-size:{$attributes['featuresFontSize']}px;";
    $css .= "}";

    $css .= ".flexguten-product-price {";
        $css .= "color:{$attributes['pricingColor']};";
        $css .= "font-size:{$attributes['pricingFontSize']}px;";
    $css .= "}";

    $css .= ".amazon-button {";
        $buttonBG = $attributes['buttonBackground'];
        $buttonFC = $attributes['buttonFontColor'];
        $buttonFS = $attributes['buttonFontSize'];
        if($buttonBG !== '' ){
            $css .= "background:{$attributes['buttonBackground']}!important;";
        }   
        if($buttonFC !== '' ){
            $css .= "color:{$buttonFC}!important;";
        }    
        if($buttonFS !== '' ){
            $css .= "font-size:{$buttonFS}!important;";
        }        
        $css .= "border:{$attributes['buttonBorder']['width']} {$attributes['buttonBorder']['style']} {$attributes['buttonBorder']['color']}!important;";
    $css .= "}";

    $css .= ".amazon-button:hover {";
        $css .= "background:{$attributes['buttonHoverBackground']}!important;";
        $css .= "color:{$attributes['buttonHoverFontColor']}!important;";
    $css .= "}";
        
    return $css;

    }

}
