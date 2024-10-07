<?php 
namespace Core\Classes\System;

use core\classes\dbWrapper\db;

class LicenseManager 
{
    
    /**
     * получаем дату окончания лицензии
     * 
     * old function name get_license_expired_date
     */
    public static function getLicenseExpiredDate() 
    {
        $res = db::select([
            'table_name' => ' licence ',
            'col_list' => ' * ',
            'query' => [
                'base_query' => '',
                'param' => '',
                'joins' => '',
            ],
            'bindList' => array()            
        ])->get();
    
        return $res[0]['licence_active_deactive'];        
    }
    
    /**
     * 
     * old function name get_license_hash
     */
    public static function getLicenseHash() 
    {
        $res = db::select([
            'table_name' => ' licence ',
            'col_list' => ' * ',
            'query' => [
                'base_query' => '',
                'param' => '',
                'joins' => '',
            ],
            'bindList' => array()                       
        ])->get();
    
        return $res[0]['licence_value'];        
    }
    
    /**
     * 
     * old function name get_license_sault_key 
     */
    public static function getLicenseSaultKey() 
    {
        $res = db::select([
            'table_name' => ' licence ',
            'col_list' => ' * ',
            'query' => [
                'base_query' => '',
                'param' => '',
                'joins' => '',
            ],            
            'bindList' => array()
        ])->get();
    
        return $res[0]['license_sault_key'];        
    }
    
    
    
    /**
     * проеверяем на лицению
     * 
     * old function name check_license_expired
     */
    public function hasLicenseExpired($today, $expired_licese_date, callable $callback) {
        if(strtotime($today) > strtotime($expired_licese_date)) {
            return $callback(true);
        } 
        
        return $callback(false);
    }

    
    /**
     * 
     */
    public function activeLicense($key, callable $callback) 
    {
        $license_hash = self::getLicenseHash();
    
        if(md5($key) == $license_hash) {
            $new_sault = rand(000000,999999);
        
            $ordertoday = date("d.m.Y");
            $deactive_date = date('d.m.Y', strtotime('+30 day'));
        
            $genetaion_hash =  $new_sault * 2 / 3;
        
            $genetaion_hash = md5((int) $genetaion_hash);
        
            $update_data = [
                'before' => 'UPDATE licence SET ',
                'after' => ' WHERE licence_active = 1 ',
                'post_list' => [
                    'hash' => [
                        'query' => ' licence_value = :licence_hash  ',
                        'bind' => 'licence_hash',
                    ],
                    'new_license_date' => [
                        'query' => ' licence_active_deactive = :new_date  ',
                        'bind' => 'new_date',
                    ],
                    'sault' => [
                        'query' => ' license_sault_key = :new_key  ',
                        'bind' => 'new_key',
                    ],
                    'today_active_date' => [
                        'query' => ' licence_active_date = :today  ',
                        'bind' => 'today',
                    ]			
                ]
            ];
        
            db::update($update_data, [
                'hash'              => $genetaion_hash,
                'new_license_date'  => $deactive_date,
                'sault'             => $new_sault,
                'today_active_date' => $ordertoday
            ]);

            return $callback(true);
        } 

        return $callback(false);
    }
    
}