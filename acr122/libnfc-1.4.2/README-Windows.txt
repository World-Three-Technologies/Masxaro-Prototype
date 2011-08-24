*-
* Public platform independent Near Field Communication (NFC) library
* Copyright (C) 2010, Glenn Ergeerts
-*

Requirements
============

- MinGW-w64 compiler toolchain [1]
- LibUsb-Win32 0.1.12.2 [2]
- CMake 2.6 [3]

This was tested on Windows 7 64 bit, but should work on Windows Vista and
Windows XP and 32 bit as well. 
Only the ACS ACR122 reader is tested at the moment, so any feedback about other devices is very welcome.

Community forum: http://www.libnfc.org/community/

Building
========

To build the distribution the MinGW Makefiles generator of CMake was used. Here
is an example of how to generate a distribution with the above mentioned
requirements fulfilled (it is assumed the CMake binaries are in the system
path, this is optional during installation of CMake):

- Add the following directories to your PATH : c:\MinGW64\bin;c:\MinGW64\x86_64-w64-mingw32\lib32;c:\MinGW64\x86_64-w64-mingw32\include

- Now it is possible to run CMake and mingw32-make:

  C:\dev\libnfc-read-only> mkdir ..\libnfc-build
  C:\dev\libnfc-read-only> cd ..\libnfc-build
  C:\dev\libnfc-build> cmake-gui .
  
Now you can configure the build. Press "Configure", specify "MinGW32 Makefiles"
and then you have the opportunity to set some configuration variables. If you
don't want a Debug build change the variable CMAKE_BUILD_TYPE to "Release".

If a non-GUI solution is preferred one can use:

  C:\dev\libnfc-build> cmake -G "MinGW Makefiles"
                                     -DCMAKE_BUILD_TYPE=Release ..\libnfc-read-only

Now run mingw32-make to build:

  C:\dev\libnfc-read-only\bin> mingw32-make
  
The build will create a shared library for Windows (nfc.dll) to link your applications against. It will compile
the tools against this shared library. 

References
==========
[1] the easiest way is to use the TDM-GCC installer. 
        Make sure to select MinGW-w64 in the installer, the regular MinGW does not contain headers for PCSC.
        http://sourceforge.net/projects/tdm-gcc/files/TDM-GCC%20Installer/tdm64-gcc-4.5.1.exe/download
[2] http://libusb-win32.sourceforge.net/
[3] http://www.cmake.org
