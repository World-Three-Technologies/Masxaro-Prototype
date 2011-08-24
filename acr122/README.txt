This is the git repository for World Three Technologies Masxaro Summer Project 2011.

This project is based on libnfc.org's open source project. Now, the libnfc version been using is 1.4.2.

To modify and compile and run the code: 
$ cd libnfc-1.4.2
$ dpkg-buildpackage -b -us -uc

Make sure you have required run-time dependencies.
$ sudo apt-get install libusb-0.1-4 libpcsclite1 libccid pcscd

Install libnfc into your system
$ sudo dpkg -i ../libnfc*.deb

Plug in NFC device, place a tag on it and test your installation
$ nfc-list