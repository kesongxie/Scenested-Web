//
//  PostCollectionViewCell.swift
//  scenested-experiment
//
//  Created by Xie kesong on 5/31/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class PostCollectionViewCell: UICollectionViewCell {

    @IBOutlet weak var imageView: UIImageView!
   
    @IBOutlet weak var postText: UILabel!
    
    
    var isGradientLayerAdded: Bool = false
    
    var imageViewSize: CGSize = CGSizeZero{
        didSet{
            //create a gradient black at the bottom of the imageView
            if !isGradientLayerAdded{
                let  gradientLayer = CAGradientLayer()
                gradientLayer.colors = StyleSchemeConstant.horizontalSlider.gradientOverlay.gradientColors
                gradientLayer.locations = StyleSchemeConstant.horizontalSlider.gradientOverlay.gradientLocation
                gradientLayer.frame = CGRect(x: 0, y: 0, width: imageViewSize.width, height: imageViewSize.height) // the frame that specified where to display and the dimension of the gradient layer.
                imageView.layer.insertSublayer(gradientLayer, atIndex: 0) //finish by adding the sublayer to its parent layer
                isGradientLayerAdded = true
            }
        }
    }
    
    
    
}
