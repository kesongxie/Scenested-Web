//
//  CloseUpAnimator.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/4/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

enum pinnedDirection{
    case rightEdge
    case leftEdge
    case middle
}

class CloseUpAnimator: NSObject, UIViewControllerAnimatedTransitioning {
    let duration = 0.35
    var presenting: Bool = false
    var selectedItemInfo =  CloseUpEffectSelectedItemInfo()
    
    func transitionDuration(transitionContext: UIViewControllerContextTransitioning?) -> NSTimeInterval {
        return duration
    }
    
    func animateTransition(transitionContext: UIViewControllerContextTransitioning) {
        let containerView = transitionContext.containerView()!
        var fromView = transitionContext.viewForKey(UITransitionContextFromViewKey)!
        let toView = transitionContext.viewForKey(UITransitionContextToViewKey)!
        
        containerView.addSubview(toView)
        containerView.bringSubviewToFront(toView)
        toView.alpha = 0
        
        
        let toViewController = transitionContext.viewControllerForKey(UITransitionContextToViewControllerKey)!

        
        
        UIApplication.sharedApplication().setStatusBarHidden(false, withAnimation: .Fade)
        
        
        if presenting{
            toView.hidden = false
            let scaleTransform = CGAffineTransformIdentity
            UIView.animateWithDuration(duration, animations: {
                toView.transform = scaleTransform
                toView.alpha = 1
                fromView.alpha = 0

                toView.frame = CGRect(x: 0 , y: 0, width: toView.frame.size.width, height: toView.frame.size.height)

                }, completion: { (finished) -> Void in
                    self.presenting = false
//                    fromView.removeFromSuperview()
                    transitionContext.completeTransition(true)
            })

        }else{
            
           // let fromViewController = transitionContext.viewControllerForKey(UITransitionContextFromViewControllerKey) as! TabBarController
//            let globalScrollView = selectedItemInfo.selectedItemParentGlobalView
            
            
            
            
//            print(globalScrollView.contentOffset.y)
//
//            let verticalScrollOffset = globalScrollView.contentOffset.y + selectedItemInfo.thumbnailFrame.origin.y + selectedItemInfo.thumbnailFrame.size.height - UIScreen.mainScreen().bounds.height + fromViewController.tabBar.frame.size.height
//            
//            
//            
//            print(verticalScrollOffset)
//            
//            let pointToScroll = CGPoint(x: 0 , y:  verticalScrollOffset)
//            
//            
//            globalScrollView.setContentOffset(pointToScroll, animated: false)
//            
            
            
            
            
            //scale transform for from view
            let fromViewTransformScaleX: CGFloat = toView.frame.size.width / selectedItemInfo.thumbnailFrame.size.width
            let fromViewTransformScaleY: CGFloat = toView.frame.size.height / selectedItemInfo.thumbnailFrame.size.height
            
            let fromViewScaleTransform = CGAffineTransformMakeScale(fromViewTransformScaleX, fromViewTransformScaleY) // enlarge the from view
           
            let fromViewOriginalSize = fromView.frame.size
            var fromViewAdjustX: CGFloat = 0
            var fromViewAdjustY: CGFloat = 0

            
            var xPinnedTo: pinnedDirection
            
            if selectedItemInfo.thumbnailFrame.origin.x < 0 {
                //fromView pinned to the left, because the thumbnail is left off the screen
                xPinnedTo = .leftEdge
                
            }else if selectedItemInfo.thumbnailFrame.origin.x + selectedItemInfo.thumbnailFrame.size.width > UIScreen.mainScreen().bounds.width{
                //fromView pinned to the right, because the thumbnail is right off the screen
                xPinnedTo = .rightEdge
            }else{
                //the whole thumbnail is on the screen
                xPinnedTo = .middle
            }

            
            
            
            UIView.animateWithDuration(duration, animations: {
                    fromView.transform = fromViewScaleTransform
                    fromView.alpha = 0
                    switch xPinnedTo{
                        case .leftEdge:
                            fromViewAdjustX = 0
                        case .rightEdge:
                            fromViewAdjustX = -(fromView.frame.size.width - fromViewOriginalSize.width)
                        case .middle:
                            fromViewAdjustX = -self.selectedItemInfo.thumbnailFrame.origin.x * fromViewTransformScaleX
                        }
                        fromView.frame = CGRect(x: fromViewAdjustX, y: -self.selectedItemInfo.thumbnailFrame.origin.y * fromViewTransformScaleY, width: fromView.frame.size.width, height: fromView.frame.size.height )
                
                        toView.alpha = 1
                }, completion: { (finished) -> Void in
                    self.presenting = true

                    transitionContext.completeTransition(true)
            })
        }
    }
}
