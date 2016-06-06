//
//  CloseUpAnimator.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/4/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CloseUpAnimator: NSObject, UIViewControllerAnimatedTransitioning {
    let duration = 0.35
    var presenting: Bool = false
    var thumbnailFrame: CGRect = CGRectZero
    
    func transitionDuration(transitionContext: UIViewControllerContextTransitioning?) -> NSTimeInterval {
        return duration
    }
    
    func animateTransition(transitionContext: UIViewControllerContextTransitioning) {
        let  containerView = transitionContext.containerView()!
        let fromView = transitionContext.viewForKey(UITransitionContextFromViewKey)!
        let toView = transitionContext.viewForKey(UITransitionContextToViewKey)!
        
        containerView.addSubview(fromView)
        containerView.addSubview(toView)
        containerView.bringSubviewToFront(toView)
        toView.hidden = true
        
        
        if presenting{
            toView.hidden = false
            let scaleTransform = CGAffineTransformIdentity
            UIView.animateWithDuration(duration, animations: {
                toView.transform = scaleTransform
                toView.alpha = 1
                toView.frame = CGRect(x: 0 , y: 0, width: toView.frame.size.width, height: toView.frame.size.height)

                }, completion: { (finished) -> Void in
                    self.presenting = false
                    fromView.removeFromSuperview()
                    transitionContext.completeTransition(true)
            })

        }else{
            let transformScaleX: CGFloat = toView.frame.size.width / thumbnailFrame.size.width
            let transformScaleY: CGFloat = toView.frame.size.height / thumbnailFrame.size.height
            
            let scaleTransform = CGAffineTransformMakeScale(transformScaleX, transformScaleY)
            
            UIView.animateWithDuration(duration, animations: {
                    fromView.transform = scaleTransform
                    fromView.alpha = 0
                    fromView.frame = CGRect(x: -self.thumbnailFrame.origin.x * transformScaleX , y: -self.thumbnailFrame.origin.y * transformScaleY, width: fromView.frame.size.width, height: 120)
                }, completion: { (finished) -> Void in
                    self.presenting = true
                    toView.hidden = false
                    transitionContext.completeTransition(true)
            })
           


        }
        
        
        
        
//        let fromView = transitionContext.viewForKey(UITransitionContextFromViewKey)!
//        let toView = transitionContext.viewForKey(UITransitionContextToViewKey)!
//        
//        if !presenting{
//            containerView.addSubview(toView)
//            toView.hidden = true
//        }
        
        
    }
    
    
}
