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

#define MAX_FRAME_LEN 		264
#define NDEF_DESC_LEN 		20   // 1+1+4+14 header(1)+typelen(1)+payloadlen(4)+type(14)
#define NDEF_NLEN     		2    // NLEN of the NDEF msg
#define MAX_SEND_PAYLOAD   	0xfb
#define HIGH_BYTE_FLAG     	1
#define LOW_BYTE_FLAG      	2

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

char* get_hi_lo(char *num, int flag) {
  char *result = (char *) malloc(2*sizeof(char));
  if (flag == 1) {
    strncpy(result, num, 2);
  }
  else {
	strncpy(result, num+2, 2);
  }
  return result;
}

void send_ndef_msg_header(int record, int msg, char *data) {
  byte_t *send_data;
  int i;
  //printf("header...\n");
  if (record > MAX_SEND_PAYLOAD) {
    send_data = (byte_t *) malloc(MAX_SEND_PAYLOAD*sizeof(byte_t));
    send_data[0] = (byte_t) ((0xff00 & msg) >> 8);			// NLEN high byte
    send_data[1] = (byte_t) (0xff & msg);	    			// NLEN low byte
    send_data[2] = 0xc4;						// 1110, 0100.
    send_data[3] = 0x0e;						// type length 14
    send_data[4] = 0x00;						// payload 3
    send_data[5] = 0x00;						// payload 2
    send_data[6] = (byte_t) ((0xff00 & (msg-NDEF_DESC_LEN)) >> 8);	// payload 1
    send_data[7] = (byte_t) (0xff & (msg-NDEF_DESC_LEN));		// payload 0
send_data[8] = 0x6d;						// type 'm'
send_data[9] = 0x61;						// type 'a'
send_data[10] = 0x73;						// type 's'
send_data[11] = 0x78;						// type 'x'
send_data[12] = 0x61;						// type 'a'
send_data[13] = 0x72;						// type 'r'
send_data[14] = 0x6f;						// type 'o'
send_data[15] = 0x2e;						// type '.'
send_data[16] = 0x63;						// type 'c'
send_data[17] = 0x6f;						// type 'o'
send_data[18] = 0x6d;						// type 'm'
send_data[19] = 0x3a;						// type ':'
send_data[20] = 0x6d;						// type 'm'
send_data[21] = 0x64;						// type 'd'
   // send_data[22] = 0x02;						// UTF-8 and 2 byte long iso "en"
    for (i = 22;i<MAX_SEND_PAYLOAD;i++) {
	  send_data[i] = (byte_t) data[i-22];
      //printf("%02x ", send_data[i]);
    }
    send_data[i++] = 0x90;
    send_data[i] = 0x00;
//    printf("send data:\n");
    send_bytes(send_data,MAX_SEND_PAYLOAD+2);
    free(send_data);
  }
  else {
    send_data = (byte_t *) malloc(MAX_SEND_PAYLOAD*sizeof(byte_t));
    send_data[0] = (byte_t) ((0xff00 & msg) >> 8);			// NLEN high byte
    send_data[1] = (byte_t) (0xff & msg);	    			// NLEN low byte
    send_data[2] = 0xc4;						// 1110, 0100.
    send_data[3] = 0x0e;						// type length 14
    send_data[4] = 0x00;						// payload 3
    send_data[5] = 0x00;						// payload 2
    send_data[6] = (byte_t) ((0xff00 & (msg-NDEF_DESC_LEN)) >> 8);	// payload 1
    send_data[7] = (byte_t) (0xff & (msg-NDEF_DESC_LEN));		// payload 0
    send_data[8] = 0x6d;						// type 'm'
send_data[9] = 0x61;						// type 'a'
send_data[10] = 0x73;						// type 's'
send_data[11] = 0x78;						// type 'x'
send_data[12] = 0x61;						// type 'a'
send_data[13] = 0x72;						// type 'r'
send_data[14] = 0x6f;						// type 'o'
send_data[15] = 0x2e;						// type '.'
send_data[16] = 0x63;						// type 'c'
send_data[17] = 0x6f;						// type 'o'
send_data[18] = 0x6d;						// type 'm'
send_data[19] = 0x3a;						// type ':'
send_data[20] = 0x6d;						// type 'm'
send_data[21] = 0x64;						// type 'd'		// type 'm'
for (i = 22;i<record;i++) {
	  send_data[i] = (byte_t) data[i-22];
      //printf("%02x ", send_data[i]);
    }	
send_data[i++] = 0x90;
    send_data[i] = 0x00;
    send_bytes(send_data,record+2);
    free(send_data);

/*
	send_data = (byte_t *) malloc(MAX_SEND_PAYLOAD*sizeof(byte_t));
    send_data[0] = 0x00;						// NLEN high byte
    send_data[1] = (byte_t) (0xff & msg);	    // NLEN low byte
    send_data[2] = 0xc1;						// Mb, Mc etc.
    send_data[3] = 0x01;						// type length
    send_data[4] = 0x00;						// payload 3
    send_data[5] = 0x00;						// payload 2
    send_data[6] = 0x00;						// payload 1
    send_data[7] = (byte_t) (0xff & (msg-NDEF_DESC_LEN));	// payload 0
    send_data[8] = 0x54;						// type 't'
    for (i = 9;i<MAX_SEND_PAYLOAD-9;i++) {
	  send_data[i] = (byte_t) data[i-9];
      //printf("%02x ", send_data[i]);
    }
    send_data[i++] = 0x90;
    send_data[i] = 0x00;
    printf("send data:\n");
    send_bytes(send_data,MAX_SEND_PAYLOAD+2);
    free(send_data);
	
	*/
  }
}

void send_ndef_rest_msg(int file_sz, char *data) {
  char *send_data = (char *) malloc((MAX_SEND_PAYLOAD+2)*sizeof(char));
  // Calculate the bytes of the file that has been sent and 
  // the rest of the file to ready be sent.
  int start = MAX_SEND_PAYLOAD - NDEF_DESC_LEN - NDEF_NLEN;
  int rest = file_sz - start;
  int i;
  //printf("rest %d", rest);
  while (rest > 0) {
	receive_bytes();
	if (rest > MAX_SEND_PAYLOAD) {
      for (i = 0;i<MAX_SEND_PAYLOAD;i++) {
	    send_data[i] = (byte_t) data[i+start];
        //printf("%02x", send_data[i]);
      }
      send_data[i++] = 0x90;
      send_data[i] = 0x00;
      send_bytes(send_data,MAX_SEND_PAYLOAD+2);
	  rest -= MAX_SEND_PAYLOAD;
	  start += MAX_SEND_PAYLOAD;
	}
	else {
	  for (i = 0;i<rest;i++) {
	    send_data[i] = (byte_t) data[i+start];
        //printf("%02x", send_data[i]);
      }
      send_data[i++] = 0x90;
      send_data[i] = 0x00;
	  send_bytes(send_data,rest+2);
	  rest = 0;
	}
  }
}

int
main (int argc, char *argv[])
{
  int ndef_record_sz, ndef_file_sz, ndef_msg_sz;
  char num[5], *high, *low;
  int fsz;
  char *jsonstr, *str;
  FILE *fp;
  char *filename;
  char c;
  int i = 0;
  
  filename = (char *) malloc(10*sizeof(char));
  fflush(stdin);
  printf("Please load a receipt:\n");
  gets(filename);
  
  if ((fp = fopen(filename, "r")) == NULL) {
	printf("can not open this file\n");
	exit(0);
  }
  ndef_file_sz = 0;
  
  fseek(fp, 0L, SEEK_END);
  fsz = ftell(fp);
  fseek(fp, 0L, SEEK_SET);
  
  str = (char *) malloc((fsz-1)*sizeof(char)); // last char is EOF.
  jsonstr = str;
  
  while (!feof(fp)) {
	c = fgetc(fp);
	if (c < 0x20)	// no \r and \n and all other unprintable symbol
	  break;
	*str = c;
	str++;
	ndef_file_sz++;
  }
  fclose(fp);
  
  ndef_record_sz = ndef_file_sz + NDEF_NLEN + NDEF_DESC_LEN;
  ndef_msg_sz = ndef_file_sz + NDEF_DESC_LEN;
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
//The possible NDEF msg size is 0x0ffe.
  send_bytes((const byte_t*)"\x00\x0f\x20\x05\xfe\x00\x34\x04\x06\xe1\x04\x05\xfe\x00\x00\x90\x00",17);
  receive_bytes();
//Receiving data: 00  a4  00  00  02  e1  04
//= Select NDEF
  send_bytes((const byte_t*)"\x90\x00",2);
  receive_bytes();
//Receiving data: 00  b0  00  00  02
//=  Read first 2 NDEF bytes
//Sent NDEF file Length, the length includes the NLEN and NDEF msg
//The NLEN is the real NDEF msg, not the whole file.
  byte_t t[4];
  t[0] = (byte_t) ((0xff00 & ndef_record_sz) >> 8);	// ndef_record_sz = ndef_file_sz + NDEF_NLEN + NDEF_DESC_LEN;
  t[1] = (byte_t) (0xff & ndef_record_sz);		// ndef_msg_sz = ndef_file_sz + NDEF_DESC_LEN;
  t[2] = 0x90;
  t[3] = 0x00;
  send_bytes(t,4);
  receive_bytes();
//Receiving data: 00  a4  00  00  02  e1  04
//=  select the NDEF file again  
  send_bytes((const byte_t*)"\x90\x00",2);
  receive_bytes();
//Receiving data: 00  b0  00  00  xx (NDEF file length)
//= Read whole of NDEF file
  send_ndef_msg_header(ndef_record_sz, ndef_msg_sz, jsonstr);
  //send_bytes((const byte_t*)"\x00\x15\xd1\x01\x11\x54\x02\x65\x6e\x5b\x7b\x22\x69\x64\x22\x3a\x22\x31\x30\x30\x22\x7d\x5d\x90\x00",25);
  //receive_bytes();
  send_ndef_rest_msg(ndef_file_sz, jsonstr);
  nfc_disconnect(pnd);
  exit (EXIT_SUCCESS);
}
