<?php
/*  
    Shopix_MobileDetect - Detect mobile device and redirect to the appropriate store view.
    Copyright (C) 2013 Shopix Pty Ltd (http://www.shopix.com.au)
   
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.
        
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.
        
    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Shopix_MobileDetect_Model_Header extends Mage_Core_Model_Abstract {
    const CONFIG_NO = 0;
    const CONFIG_YES = 1;
    const CONFIG_MOBILE_ONLY = 2;
}

