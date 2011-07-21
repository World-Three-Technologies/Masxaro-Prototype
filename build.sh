 #  build.sh -- web project build file 
 #
 #  Copyright 2011 World Three Technologies, Inc. 
 #  All Rights Reserved.
 # 
 #  This program is free software; you can redistribute it and/or modify
 #  it under the terms of the GNU General Public License as published by
 #  the Free Software Foundation; either version 2 of the License, or
 #  (at your option) any later version.
 #
 #  This program is distributed in the hope that it will be useful,
 #  but WITHOUT ANY WARRANTY; without even the implied warranty of
 #  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 #  GNU General Public License for more details.
 #
 #  You should have received a copy of the GNU General Public License
 #  along with this program; if not, write to the Free Software
 #  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 #
 #  Written by Yaxing Chen <Yaxing@masxaro.com>
 #
set -e
echo "searching root path..."
ROOT=$(sudo find / -name htdocs)
if [ -z $ROOT ]; then
        ROOT=$(sudo find / -wholename /var/www/html)
        if [ -z $ROOT ]; then
                echo "error: cannot find root path(/var/www/html or htdocs).\n exit."
                exit
        fi
fi
echo "root path found."
echo copying files...
sudo cp -r ./proj/ $ROOT/masxaro/
echo "ROOT PATH: $ROOT/masxaro/ (http://localhost/masxaro/)"
echo "done"