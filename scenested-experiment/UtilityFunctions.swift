//
//  UtilityFunctions.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/6/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation
import UIKit

enum UploadPhotoFor{
    case profileAvator
    case profileCover
    case themeCover
    case profilePost
    case none
}


func getAspectRatioFromSize(size: CGSize) -> CGFloat{
    return size.width / size.height
}
