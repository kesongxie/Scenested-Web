//
//  CloseUpEffectSelectedItemInfo.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/6/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation
import UIKit

//the information the selected item holds, such as frame and the thumbnail image size
struct CloseUpEffectSelectedItemInfo{
    var thumbnailFrame: CGRect = CGRectZero
    var thumbnailImageAspectRatio: CGFloat = 1
    var thumbnailImageView: UIImageView = UIImageView()
    var thumbnailIndexPath: NSIndexPath = NSIndexPath()
//    var selectedItemParentGlobalView = UIScrollView()
}
