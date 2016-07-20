//
//  UIImageExtension.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/27/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

extension UIImage{
    var aspectRatio: CGFloat{
        get{
            return getAspectRatioFromSize(self.size)
        }
    }
    
    func normalizedImage()-> UIImage?{
        if self.imageOrientation == .Up{
            return self
        }else{
            UIGraphicsBeginImageContextWithOptions(self.size, true, self.scale)
            self.drawInRect(CGRect(origin: CGPoint(x: 0,y: 0), size: self.size))
            let normalizedImage = UIGraphicsGetImageFromCurrentImageContext()

            UIGraphicsEndImageContext()
            return normalizedImage
        }
    }
}

