//
//  RichTextView.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/22/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

let HashTagAttributeName = "hashtag"
let MentionedAttributeName = "mentioned"


extension UITextView {

    func setStyleText(text: String){
        let paragraphStyle = NSMutableParagraphStyle()
        paragraphStyle.lineSpacing = 2.8
        
        let font = UIFont.systemFontOfSize(16)
        
        let attributes = [
            NSParagraphStyleAttributeName: paragraphStyle,
            NSFontAttributeName: font
        ]
        
        
        let mutableAttributedString = NSMutableAttributedString(string: text)
        mutableAttributedString.addAttributes(attributes, range: text.fullRange())
        self.attributedText = mutableAttributedString

        
        let words = self.text.componentsSeparatedByCharactersInSet(NSCharacterSet.whitespaceAndNewlineCharacterSet())
        
        let hashTagOrMentionedRegx = try! NSRegularExpression(pattern: "[#|@][\\w]+", options: .CaseInsensitive)
        
        for word in words.filter({ hashTagOrMentionedRegx.numberOfMatchesInString($0, options: [], range: $0.fullRange()) > 0 }){
            let matches = hashTagOrMentionedRegx.matchesInString(word, options: [], range: word.fullRange())
            for match in matches{
                let stringToBeHighlighted = (word as NSString).substringWithRange(match.rangeAtIndex(0))
               let keyword = (stringToBeHighlighted as NSString).substringFromIndex(1)
                //get the range of the highlighted string
                let range = (self.text as NSString).rangeOfString(stringToBeHighlighted) //return NSString when the receiver is a NSString, otherwise if it's Stirng, this return Range<index>
                
                mutableAttributedString.addAttributes([NSForegroundColorAttributeName: StyleSchemeConstant.linkColor], range: range)
                
                if stringToBeHighlighted.containsString("#"){
                    mutableAttributedString.addAttribute(HashTagAttributeName, value: keyword, range: range)
                }else if stringToBeHighlighted.containsString("@"){
                    mutableAttributedString.addAttribute(MentionedAttributeName, value: keyword, range: range)
                }
            }
            
        }
        
        self.attributedText = mutableAttributedString

        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(UITextView.textViewTapped))
        self.addGestureRecognizer(tapGesture)

    }
    
    
    
    func textViewTapped(tapGesture: UITapGestureRecognizer){
        let point = tapGesture.locationInView(self)
        self.selectable = true
        if let position = closestPositionToPoint(point){
            let range = tokenizer.rangeEnclosingPosition(position, withGranularity: .Word, inDirection: 1)
            if range != nil {
                let location = offsetFromPosition(beginningOfDocument, toPosition: range!.start)
                let length = offsetFromPosition(range!.start, toPosition: range!.end)
                let attrRange = NSMakeRange(location, length)
                let word = attributedText.attributedSubstringFromRange(attrRange)
                //                word.addAttribute(NSForegroundColorAttributeName, value: StyleSchemeConstant.linkColorWhenTapped, range: NSMakeRange(0, word.length))
//                
//                 let mutableAttributedString = NSMutableAttributedString(string: text)
//                mutableAttributedString.addAttribute(NSForegroundColorAttributeName, value: StyleSchemeConstant.linkColorWhenTapped, range: attrRange)
//                self.attributedText.add
//                
                if let hashTagValue = word.attribute(HashTagAttributeName, atIndex: 0, effectiveRange: nil ) {
                    print(hashTagValue)
                }else if let mentionedValue = word.attribute(MentionedAttributeName, atIndex: 0, effectiveRange: nil ){
                    print(mentionedValue)
                }
              
                
            }
        }
        self.selectable = false

        

        
    }
    
    
    
    
    
    
    /*
    // Only override drawRect: if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    override func drawRect(rect: CGRect) {
        // Drawing code
    }
    */

}
