//
//  ThemeCollectionViewCell.swift
//  scenested-experiment
//
//  Created by Xie kesong on 4/29/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class ThemeCollectionViewCell: UICollectionViewCell {
    
    @IBOutlet weak var themeImage: ThemeImageView!
    
    @IBOutlet weak var themeName: UILabel!
    
    var isGradientLayerAdded: Bool = false //prevent from adding gradient layer over and over again

    var imageViewSize: CGSize = CGSizeZero{
        didSet{
            if !isGradientLayerAdded{
                let  gradientLayer = CAGradientLayer()
                gradientLayer.colors = StyleSchemeConstant.horizontalSlider.gradientOverlay.gradientColors
                gradientLayer.locations = StyleSchemeConstant.horizontalSlider.gradientOverlay.gradientLocation
                gradientLayer.frame = CGRect(x: 0, y: 0, width: imageViewSize.width, height: imageViewSize.height) // the frame that specified where to display and the dimension of the gradient layer.
                themeImage.layer.insertSublayer(gradientLayer, atIndex: 0) //finish by adding the sublayer to its parent layer
                isGradientLayerAdded = true
            }
        }
    }
    
  
    

}
