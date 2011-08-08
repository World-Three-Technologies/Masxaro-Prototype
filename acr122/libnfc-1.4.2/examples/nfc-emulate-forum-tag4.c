/*-
 * Public platform independent Near Field Communication (NFC) library examples
 * 
 * Copyright (C) 2010, Roel Verdult, Romuald Conty
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
 * @file nfc-emulate-forum-tag4.c
 * @brief Emulates a NFC Forum Tag Type 4 with a NDEF message
 */

// Notes & differences with nfc-emulate-tag:
// - This example only works with PN532 because it relies on
//   its internal handling of ISO14443-4 specificities.
// - Thanks to this internal handling & injection of WTX frames,
//   this example works on readers very strict on timing
// - This example expects a hardcoded list of commands and
//   more precisely the commands sent by a Nokia NFC when
//   discovering a NFC-Forum tag type4:
//   * Anticoll & RATS
//   * App Select by name "e103e103e103"
//   * App Select by name "e103e103e103"
//   * App Select by name "D2760000850100"
//   * Select CC
//   * ReadBinary CC
//   * Select NDEF
//   * Read first 2 NDEF bytes
//   * Read remaining of NDEF file

#ifdef HAVE_CONFIG_H
#  include "config.h"
#endif // HAVE_CONFIG_H

#include <stdio.h>
#include <stdlib.h>
#include <stddef.h>
#include <stdint.h>
#include <string.h>

#include <nfc/nfc.h>

#include <nfc/nfc-messages.h>
#include "nfc-utils.h"

#define MAX_FRAME_LEN 264

static byte_t abtRx[MAX_FRAME_LEN];
static size_t szRx;
static nfc_device_t *pnd;
static bool quiet_output = false;

#define SYMBOL_PARAM_fISO14443_4_PICC   0x20

bool send_bytes (const byte_t * pbtTx, const size_t szTx)
{
  // Show transmitted command
  if (!quiet_output) {
    printf ("Sent data: ");
    print_hex (pbtTx, szTx);
  }

  // Transmit the command bytes
  if (!nfc_target_send_bytes(pnd, pbtTx, szTx)) {
    nfc_perror (pnd, "nfc_target_send_bytes");
    exit(EXIT_FAILURE);
  }
  // Succesful transfer
  return true;
}

bool receive_bytes (void)
{
  if (!nfc_target_receive_bytes(pnd,abtRx,&szRx)) {
    nfc_perror (pnd, "nfc_target_receive_bytes");
    exit(EXIT_FAILURE);
  }

  // Show received answer
  if (!quiet_output) {
    printf ("Received data: ");
    print_hex (abtRx, szRx);
  }
  // Succesful transfer
  return true;
}

int
main (int argc, char *argv[])
{
  // Try to open the NFC reader
  pnd = nfc_connect (NULL);

  if (pnd == NULL) {
    ERR("Unable to connect to NFC device");
    return EXIT_FAILURE;
  }

  printf ("Connected to NFC device: %s\n", pnd->acName);
  printf ("Emulating NDEF tag now, please touch it with a second NFC device\n");

  nfc_target_t nt = {
    .nm.nmt = NMT_ISO14443A,
    .nm.nbr = NBR_UNDEFINED, // Will be updated by nfc_target_init()
    .nti.nai.abtAtqa = { 0x00, 0x04 },
    .nti.nai.abtUid = { 0x08, 0x00, 0xb0, 0x0b },
    .nti.nai.btSak = 0x20,
    .nti.nai.szUidLen = 4,
    .nti.nai.szAtsLen = 0,
  };

  if (!nfc_target_init (pnd, &nt, abtRx, &szRx)) {
    nfc_perror (pnd, "nfc_target_init");
    ERR("Could not come out of auto-emulation, no command was received");
    return EXIT_FAILURE;
  }

  if (!quiet_output) {
    printf ("Received data: ");
    print_hex (abtRx, szRx);
  }

//Receiving data: e0  40
//= RATS, FSD=48
//Actually PN532 already sent back the ATS so nothing to send now
  receive_bytes();
//Receiving data: 00  a4  04  00  07  d2  76  00  00  85  01  00
//= App Select by name "D2760000850100"
  send_bytes((const byte_t*)"\x90\x00",2);
  receive_bytes();
//Receiving data: 00  a4  00  00  02  e1  03
//= Select CC
  send_bytes((const byte_t*)"\x90\x00",2);
  receive_bytes();
//Receiving data: 00  b0  00  00  0f
//= ReadBinary CC
//We send CC + OK
  send_bytes((const byte_t*)"\x00\x0f\x20\x00\x3b\x00\x34\x04\x06\xe1\x04\x0e\xe0\x00\x00\x90\x00",17);
  receive_bytes();
//Receiving data: 00  a4  00  00  02  e1  04
//= Select NDEF
  send_bytes((const byte_t*)"\x90\x00",2);
  receive_bytes();
//Receiving data: 00  b0  00  00  02
//=  Read first 2 NDEF bytes
//Sent NDEF Length=0x21
  send_bytes((const byte_t*)"\x00\x20\x90\x00",4);
  receive_bytes();
  send_bytes((const byte_t*)"\x90\x00",2);
  receive_bytes();
//Receiving data: 00  b0  00  00  02
//=  Read first 2 NDEF bytes
//Sent NDEF Length=0x21
  
  
//Receiving data: 00  b0  00  02  21
//= Read remaining of NDEF file
  send_bytes((const byte_t*)"\xd1\x01\x1c\x54\x02\x65\x6e\x53\x6f\x6d\x65\x20\x72\x61\x6e\x64\x6f\x6d\x20\x65\x6e\x67\x6c\x69\x73\x68\x20\x74\x65\x78\x74\x2e\x90\x00",34);

  nfc_disconnect(pnd);
  exit (EXIT_SUCCESS);
}
