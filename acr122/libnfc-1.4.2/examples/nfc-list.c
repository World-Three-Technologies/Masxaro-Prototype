/*-
 * Public platform independent Near Field Communication (NFC) library examples
 * 
 * Copyright (C) 2009, Roel Verdult
 * Copyright (C) 2010, Romuald Conty, Romain Tartière
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *  1) Redistributions of source code must retain the above copyright notice,
 *  this list of conditions and the following disclaimer. 
 *  2 )Redistributions in binary form must reproduce the above copyright
 *  notice, this list of conditions and the following disclaimer in the
 *  documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * Note that this license only applies on the examples, NFC library itself is under LGPL
 *
 */

/**
 * @file nfc-list.c
 * @brief Lists the first target present of each founded device
 */

#ifdef HAVE_CONFIG_H
#  include "config.h"
#endif // HAVE_CONFIG_H

#ifdef HAVE_LIBUSB
#  ifdef DEBUG
#    include <sys/param.h>
#    include <usb.h>
#  endif
#endif

#include <err.h>
#include <stdio.h>
#include <stddef.h>
#include <stdlib.h>
#include <string.h>

#include <nfc/nfc.h>
#include <nfc/nfc-messages.h>
#include "nfc-utils.h"

#define MAX_DEVICE_COUNT 16
#define MAX_TARGET_COUNT 16

static nfc_device_t *pnd;

int
main (int argc, const char *argv[])
{
  const char *acLibnfcVersion;
  size_t  szDeviceFound;
  size_t  szTargetFound;
  size_t  i;
  bool verbose = false;
  nfc_device_desc_t *pnddDevices;

  // Display libnfc version
  acLibnfcVersion = nfc_version ();
  printf ("%s use libnfc %s\n", argv[0], acLibnfcVersion);

  pnddDevices = parse_args (argc, argv, &szDeviceFound, &verbose);
#ifdef HAVE_LIBUSB
#  ifdef DEBUG
  usb_set_debug (4);
#  endif
#endif

  /* Lazy way to open an NFC device */
#if 0
  pnd = nfc_connect (NULL);
#endif

  /* If specific device is wanted, i.e. an ARYGON device on /dev/ttyUSB0 */
#if 0
  nfc_device_desc_t ndd;
  ndd.pcDriver = "ARYGON";
  ndd.pcPort = "/dev/ttyUSB0";
  ndd.uiSpeed = 115200;
  pnd = nfc_connect (&ndd);
#endif

  /* If specific device is wanted, i.e. a SCL3711 on USB */
#if 0
  nfc_device_desc_t ndd;
  ndd.pcDriver = "PN533_USB";
  strcpy(ndd.acDevice, "SCM Micro / SCL3711-NFC&RW");
  pnd = nfc_connect (&ndd);
#endif

  if (szDeviceFound == 0) {
    if (!(pnddDevices = malloc (MAX_DEVICE_COUNT * sizeof (*pnddDevices)))) {
      fprintf (stderr, "malloc() failed\n");
      return EXIT_FAILURE;
    }

    nfc_list_devices (pnddDevices, MAX_DEVICE_COUNT, &szDeviceFound);
  }

  if (szDeviceFound == 0) {
    printf ("No NFC device found.\n");
  }

  for (i = 0; i < szDeviceFound; i++) {
    nfc_target_t ant[MAX_TARGET_COUNT];
    pnd = nfc_connect (&(pnddDevices[i]));

    if (pnd == NULL) {
      ERR ("%s", "Unable to connect to NFC device.");
      return EXIT_FAILURE;
    }
    nfc_initiator_init (pnd);

    printf ("Connected to NFC device: %s\n", pnd->acName);

    // List ISO14443A targets
    nfc_modulation_t nm = {
      .nmt = NMT_ISO14443A,
      .nbr = NBR_106,
    };
    if (nfc_initiator_list_passive_targets (pnd, nm, ant, MAX_TARGET_COUNT, &szTargetFound)) {
      size_t  n;
      if (verbose || (szTargetFound > 0)) {
        printf ("%d ISO14443A passive target(s) was found%s\n", (int) szTargetFound, (szTargetFound == 0) ? ".\n" : ":");
      }
      for (n = 0; n < szTargetFound; n++) {
        print_nfc_iso14443a_info (ant[n].nti.nai, verbose);
        printf ("\n");
      }
    }

    nm.nmt = NMT_FELICA;
    nm.nbr = NBR_212;
    // List Felica tags
    if (nfc_initiator_list_passive_targets (pnd, nm, ant, MAX_TARGET_COUNT, &szTargetFound)) {
      size_t  n;
      if (verbose || (szTargetFound > 0)) {
        printf ("%d Felica (212 kbps) passive target(s) was found%s\n", (int) szTargetFound,
                (szTargetFound == 0) ? ".\n" : ":");
      }
      for (n = 0; n < szTargetFound; n++) {
        print_nfc_felica_info (ant[n].nti.nfi, verbose);
        printf ("\n");
      }
    }

    nm.nbr = NBR_424;
    if (nfc_initiator_list_passive_targets (pnd, nm, ant, MAX_TARGET_COUNT, &szTargetFound)) {
      size_t  n;
      if (verbose || (szTargetFound > 0)) {
        printf ("%d Felica (424 kbps) passive target(s) was found%s\n", (int) szTargetFound,
                (szTargetFound == 0) ? ".\n" : ":");
      }
      for (n = 0; n < szTargetFound; n++) {
        print_nfc_felica_info (ant[n].nti.nfi, verbose);
        printf ("\n");
      }
    }

    nm.nmt = NMT_ISO14443B;
    nm.nbr = NBR_106;
    // List ISO14443B targets
    if (nfc_initiator_list_passive_targets (pnd, nm, ant, MAX_TARGET_COUNT, &szTargetFound)) {
      size_t  n;
      if (verbose || (szTargetFound > 0)) {
        printf ("%d ISO14443B passive target(s) was found%s\n", (int) szTargetFound, (szTargetFound == 0) ? ".\n" : ":");
      }
      for (n = 0; n < szTargetFound; n++) {
        print_nfc_iso14443b_info (ant[n].nti.nbi, verbose);
        printf ("\n");
      }
    }

    nm.nmt = NMT_JEWEL;
    nm.nbr = NBR_106;
    // List Jewel targets
    if (nfc_initiator_list_passive_targets(pnd, nm, ant, MAX_TARGET_COUNT, &szTargetFound )) {
      size_t n;
      if (verbose || (szTargetFound > 0)) {
        printf("%d Jewel passive target(s) was found%s\n", (int)szTargetFound, (szTargetFound==0)?".\n":":");
      }
      for(n=0; n<szTargetFound; n++) {
        print_nfc_jewel_info (ant[n].nti.nji, verbose);
        printf("\n");
      }
    }
    nfc_disconnect (pnd);
  }

  free (pnddDevices);
  return 0;
}
