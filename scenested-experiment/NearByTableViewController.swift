//
//  NearByTableViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/24/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit
import CoreBluetooth
import CoreLocation

let reuseIden = "NearByCell"

class NearByTableViewController: UITableViewController {


    
    let centralManger = CBCentralManager() //for bluetooth scanning
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.tableView.estimatedRowHeight = self.tableView.rowHeight
        self.tableView.rowHeight = UITableViewAutomaticDimension
        
        

        //bluetooth peripheral scan
        
        centralManger.delegate = self
        let cBUUID = CBUUID(NSUUID: NSUUID(UUIDString: APPUUID)!)
        
        
        centralManger.scanForPeripheralsWithServices([cBUUID], options: [CBCentralManagerScanOptionAllowDuplicatesKey: true])
        
        
        
        
        
        
        // Uncomment the following line to preserve selection between presentations
        // self.clearsSelectionOnViewWillAppear = false

        // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
        // self.navigationItem.rightBarButtonItem = self.editButtonItem()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }

    // MARK: - Table view data source

    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return 1
    }

//    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
//        // #warning Incomplete implementation, return the number of rows
//        return nearbyUser.count
//    }
//
//    
//    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
//        let cell = tableView.dequeueReusableCellWithIdentifier(reuseIden, forIndexPath: indexPath) as! NearByTableViewCell
//
//        cell.user = nearbyUser[indexPath.row]
//        
//        //Configure the cell...
//
//        return cell
//    }
//    
    
    
    func isBluetoothAuthorizedForApp() -> Bool{
       // check whether the app is allowed to use bluetooth sharing
        let authStatus = CBPeripheralManager.authorizationStatus()
        switch authStatus{
        case .Authorized:
            print("bluetooth authorized")
            return true
        case .NotDetermined:
            print("bluetooth Not determined")
        case .Denied:
            print("bluetooth denied")
            //denied
            break
        case .Restricted:
            print("bluetooth restricted")
            //restricted
            break
        }
        return false
    }

    /*
    // Override to support conditional editing of the table view.
    override func tableView(tableView: UITableView, canEditRowAtIndexPath indexPath: NSIndexPath) -> Bool {
        // Return false if you do not want the specified item to be editable.
        return true
    }
    */

    /*
    // Override to support editing the table view.
    override func tableView(tableView: UITableView, commitEditingStyle editingStyle: UITableViewCellEditingStyle, forRowAtIndexPath indexPath: NSIndexPath) {
        if editingStyle == .Delete {
            // Delete the row from the data source
            tableView.deleteRowsAtIndexPaths([indexPath], withRowAnimation: .Fade)
        } else if editingStyle == .Insert {
            // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
        }    
    }
    */

    /*
    // Override to support rearranging the table view.
    override func tableView(tableView: UITableView, moveRowAtIndexPath fromIndexPath: NSIndexPath, toIndexPath: NSIndexPath) {

    }
    */

    /*
    // Override to support conditional rearranging of the table view.
    override func tableView(tableView: UITableView, canMoveRowAtIndexPath indexPath: NSIndexPath) -> Bool {
        // Return false if you do not want the item to be re-orderable.
        return true
    }
    */

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}




extension NearByTableViewController: CBCentralManagerDelegate{
    
    func centralManagerDidUpdateState(central: CBCentralManager) {
        print("state updated")
    }
    
    func centralManager(central: CBCentralManager, didDiscoverPeripheral peripheral: CBPeripheral, advertisementData: [String : AnyObject], RSSI: NSNumber) {
        print("the advertisment data is")
        print(advertisementData)
    }
    
    
}
