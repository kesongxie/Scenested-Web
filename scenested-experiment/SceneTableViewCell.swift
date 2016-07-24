//
//  SceneTableViewCell.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/21/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class SceneTableViewCell: UITableViewCell {
    
    
    @IBOutlet weak var postUserImageView: UIImageView!{
        didSet{
            postUserImageView.layer.cornerRadius = postUserImageView.frame.size.width / 2
            postUserImageView.clipsToBounds = true
        }
    }
    
    
    @IBOutlet weak var postUserLabel: UILabel!
    
    @IBOutlet weak var postPictureImageView: UIImageView!
    
    @IBOutlet weak var postPictureHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var postTime: UILabel!
    
    
    @IBOutlet weak var themeNameLabel: UILabel!
    
    
    
    @IBOutlet weak var descriptionTextView: UITextView!
    
    //@IBOutlet weak var textViewHeightConstraint: NSLayoutConstraint!
  
    
    //  @IBOutlet weak var descriptionLabel: UILabel!
    
    
    @IBOutlet weak var commentUserImageView: UIImageView!{
        didSet{
            commentUserImageView.layer.cornerRadius = commentUserImageView.frame.size.width / 2
            commentUserImageView.clipsToBounds = true
        }
    }

    
    
    var scenePictureUrl: String?{
        didSet{
            if let url = scenePictureUrl{
                postPictureImageView.image = UIImage(named: url)
                if let picSize = postPictureImageView.image?.size{
                    postPictureImageView.frame.size.width = UIScreen.mainScreen().bounds.size.width
                    postPictureHeightConstraint.constant = postPictureImageView.bounds.size.width * picSize.height / picSize.width
                }
            }

        }
    }
    
    var themeName: String?{
        didSet{
            themeNameLabel.text = themeName
        }
    }
    
    var descriptionText: String?{
        didSet{
            descriptionTextView.setStyleText(descriptionText!)
        }
    }
    
    var postUserName: String?{
        didSet{
            postUserLabel.text = postUserName
        }
    }
    
    
    var postTimeText: String?{
        didSet{
           // postTime.text = postTimeText
        }
    }
    
    
    override func awakeFromNib() {
        super.awakeFromNib()

        // Initialization code
    }

    override func setSelected(selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
