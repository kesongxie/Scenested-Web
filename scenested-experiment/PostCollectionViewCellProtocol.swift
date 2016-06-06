//
//  PostCollectionViewCellProtocol.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/5/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation
import UIKit

protocol PostCollectionViewProtocol {
    func didTapCell(collectionView: UICollectionView, indexPath: NSIndexPath, scene: Scene, selectedThumbnailFrame: CGRect)
}
