//
//  CloseUpAnimator.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/4/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CloseUpAnimator: NSObject, UIViewControllerAnimatedTransitioning {
    let duration = 0.3
    let presenting: Bool = false
    
    func transitionDuration(transitionContext: UIViewControllerContextTransitioning?) -> NSTimeInterval {
        return duration
    }
    
    func animateTransition(transitionContext: UIViewControllerContextTransitioning) {
        let  containerView = transitionContext.containerView()!
        let fromView = transitionContext.viewForKey(UITransitionContextFromViewKey)!
        let toView = transitionContext.viewForKey(UITransitionContextToViewKey)!
        
        containerView.addSubview(toView)
        containerView.bringSubviewToFront(toView)
        transitionContext.completeTransition(true)
        
        
        
//        let fromView = transitionContext.viewForKey(UITransitionContextFromViewKey)!
//        let toView = transitionContext.viewForKey(UITransitionContextToViewKey)!
//        
//        if !presenting{
//            containerView.addSubview(toView)
//            toView.hidden = true
//        }
        
        
    }
    
    
}
