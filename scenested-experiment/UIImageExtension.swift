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
}

