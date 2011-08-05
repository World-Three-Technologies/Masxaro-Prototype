/**
 * Public platform independent Near Field Communication (NFC) library
 * 
 * Copyright (C) 2009, Roel Verdult
 * Copyright (C) 2010, Romain Tartière, Romuald Conty
 * 
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * 
 * 
 * @file nfc-types.h
 * @brief Define NFC types
 */

#ifndef __NFC_TYPES_H__
#  define __NFC_TYPES_H__

/**
 * @file types.h
 * @brief libnfc-defined types
 *
 * Define libnfc specific types: typedef, enum, struct, etc.
 */
#  include <stddef.h>
#  include <stdint.h>
#  include <stdbool.h>
#  include <stdio.h>

typedef uint8_t byte_t;

typedef enum {
  NC_PN531 = 0x10,
  NC_PN532 = 0x20,
  NC_PN533 = 0x30,
} nfc_chip_t;

struct driver_callbacks;        // Prototype the callback struct

typedef void *nfc_device_spec_t;        // Device connection specification

#  define DEVICE_NAME_LENGTH  256
/**
 * @struct nfc_device_t
 * @brief NFC device information
 */
typedef struct {
/** Callback functions for handling device specific wrapping */
  const struct driver_callbacks *pdc;
/** Device name string, including device wrapper firmware */
  char    acName[DEVICE_NAME_LENGTH];
/** PN53X chip type, this is useful for some "bug" work-arounds */
  nfc_chip_t nc;
/** Pointer to the device connection specification */
  nfc_device_spec_t nds;
/** This represents if the PN53X device was initialized succesful */
  bool    bActive;
/** Is the crc automaticly added, checked and removed from the frames */
  bool    bCrc;
/** Does the PN53x chip handles parity bits, all parities are handled as data */
  bool    bPar;
/** Should the PN53x chip handle frames encapsulation and chaining */
  bool    bEasyFraming;
/** Should the PN53x chip switch automatically in ISO14443-4 when ISO14443 */
  bool    bAutoIso14443_4;
/** Register cache for REG_CIU_BIT_FRAMING, SYMBOL_TX_LAST_BITS: The last TX bits setting, we need to reset this if it does not apply anymore */
  uint8_t ui8TxBits;
/** Register cache for SetParameters function. */
  uint8_t ui8Parameters;
/** Supported modulation encoded in a byte */
  byte_t  btSupportByte;
/** Last error reported by the PCD / encountered by the PCD driver
 * MSB       LSB
 *  | 00 | 00 |
 *    ||   ||
 *    ||   ++----- Chip-level error (as reported by the PCD)
 *    |+---------- Driver-level specific error
 *    +----------- Driver-level general error (common to all drivers)
 */
  int     iLastError;
} nfc_device_t;


/**
 * @struct nfc_device_desc_t
 * @brief NFC device description
 *
 * This struct is used to try to connect to a specified nfc device when nfc_connect(...)
 */
typedef struct {
  /** Device name (e.g. "ACS ACR 38U-CCID 00 00") */
  char    acDevice[DEVICE_NAME_LENGTH];
  /** Driver name (e.g. "PN532_UART")*/
  char   *pcDriver;
  /** Port (e.g. "/dev/ttyUSB0") */
  char   *pcPort;
  /** Port speed (e.g. "115200") */
  uint32_t uiSpeed;
  /** Device index for backward compatibility (used to choose one specific device in USB or PSCS devices list) */
  uint32_t uiBusIndex;
} nfc_device_desc_t;

/**
 * @struct chip_callbacks
 * @brief Functions for chip specific functions.
 */
struct chip_callbacks {
    /** Error lookup */
  const char *(*strerror) (const nfc_device_t * pnd);
};

/**
 * @struct driver_callbacks
 * @brief Generic structure to handle NFC device functions.
 */
struct driver_callbacks {
  /** Driver name */
  const char *acDriver;
  /** Chip specific callback functions */
  const struct chip_callbacks *pcc;
  /** Pick devices callback */
  nfc_device_desc_t *(*pick_device) (void);
  /** List devices callback */
  bool (*list_devices) (nfc_device_desc_t pnddDevices[], size_t szDevices, size_t * pszDeviceFound);
  /** Connect callback */
  nfc_device_t *(*connect) (const nfc_device_desc_t * pndd);
  /** Init callback */
  void (*init) (nfc_device_t * pnd);
  /** Transceive callback */
  bool (*transceive) (nfc_device_t * pnd, const byte_t * pbtTx, const size_t szTx, byte_t * pbtRx, size_t * pszRx);
  /** Disconnect callback */
  void    (*disconnect) (nfc_device_t * pnd);
};

// Compiler directive, set struct alignment to 1 byte_t for compatibility
#  pragma pack(1)

/**
 * @enum nfc_device_option_t
 * @brief NFC device option
 */
typedef enum {
/** Let the PN53X chip handle the CRC bytes. This means that the chip appends
 * the CRC bytes to the frames that are transmitted. It will parse the last
 * bytes from received frames as incoming CRC bytes. They will be verified
 * against the used modulation and protocol. If an frame is expected with
 * incorrect CRC bytes this option should be disabled. Example frames where
 * this is useful are the ATQA and UID+BCC that are transmitted without CRC
 * bytes during the anti-collision phase of the ISO14443-A protocol. */
  NDO_HANDLE_CRC = 0x00,
/** Parity bits in the network layer of ISO14443-A are by default generated and
 * validated in the PN53X chip. This is a very convenient feature. On certain
 * times though it is useful to get full control of the transmitted data. The
 * proprietary MIFARE Classic protocol uses for example custom (encrypted)
 * parity bits. For interoperability it is required to be completely
 * compatible, including the arbitrary parity bits. When this option is
 * disabled, the functions to communicating bits should be used. */
  NDO_HANDLE_PARITY = 0x01,
/** This option can be used to enable or disable the electronic field of the
 * NFC device. */
  NDO_ACTIVATE_FIELD = 0x10,
/** The internal CRYPTO1 co-processor can be used to transmit messages
 * encrypted. This option is automatically activated after a successful MIFARE
 * Classic authentication. */
  NDO_ACTIVATE_CRYPTO1 = 0x11,
/** The default configuration defines that the PN53X chip will try indefinitely
 * to invite a tag in the field to respond. This could be desired when it is
 * certain a tag will enter the field. On the other hand, when this is
 * uncertain, it will block the application. This option could best be compared
 * to the (NON)BLOCKING option used by (socket)network programming. */
  NDO_INFINITE_SELECT = 0x20,
/** If this option is enabled, frames that carry less than 4 bits are allowed.
 * According to the standards these frames should normally be handles as
 * invalid frames. */
  NDO_ACCEPT_INVALID_FRAMES = 0x30,
/** If the NFC device should only listen to frames, it could be useful to let
 * it gather multiple frames in a sequence. They will be stored in the internal
 * FIFO of the PN53X chip. This could be retrieved by using the receive data
 * functions. Note that if the chip runs out of bytes (FIFO = 64 bytes long),
 * it will overwrite the first received frames, so quick retrieving of the
 * received data is desirable. */
  NDO_ACCEPT_MULTIPLE_FRAMES = 0x31,
/** This option can be used to enable or disable the auto-switching mode to
 * ISO14443-4 is device is compliant.
 * In initiator mode, it means that NFC chip will send RATS automatically when
 * select and it will automatically poll for ISO14443-4 card when ISO14443A is
 * requested.
 * In target mode, with a NFC chip compiliant (ie. PN532), the chip will
 * emulate a 14443-4 PICC using hardware capability */
  NDO_AUTO_ISO14443_4 = 0x40,
/** Use automatic frames encapsulation and chaining. */
  NDO_EASY_FRAMING = 0x41,
/** Force the chip to switch in ISO14443-A */
  NDO_FORCE_ISO14443_A = 0x42,
} nfc_device_option_t;

/**
 * @enum nfc_dep_mode_t
 * @brief NFC D.E.P. (Data Exchange Protocol) active/passive mode
 */
typedef enum {
  NDM_UNDEFINED = 0,
  NDM_PASSIVE,
  NDM_ACTIVE,
} nfc_dep_mode_t;

/**
 * @struct nfc_dep_info_t
 * @brief NFC target information in D.E.P. (Data Exchange Protocol) see ISO/IEC 18092 (NFCIP-1)
 */
typedef struct {
/** NFCID3 */
  byte_t  abtNFCID3[10];
/** DID */
  byte_t  btDID;
/** Supported send-bit rate */
  byte_t  btBS;
/** Supported receive-bit rate */
  byte_t  btBR;
/** Timeout value */
  byte_t  btTO;
/** PP Parameters */
  byte_t  btPP;
/** General Bytes */
  byte_t  abtGB[48];
  size_t  szGB;
/** DEP mode */
  nfc_dep_mode_t ndm;
} nfc_dep_info_t;

/**
 * @struct nfc_iso14443a_info_t
 * @brief NFC ISO14443A tag (MIFARE) information
 */
typedef struct {
  byte_t  abtAtqa[2];
  byte_t  btSak;
  size_t  szUidLen;
  byte_t  abtUid[10];
  size_t  szAtsLen;
  byte_t  abtAts[254]; // Maximal theoretical ATS is FSD-2, FSD=256 for FSDI=8 in RATS
} nfc_iso14443a_info_t;

/**
 * @struct nfc_felica_info_t
 * @brief NFC FeLiCa tag information
 */
typedef struct {
  size_t  szLen;
  byte_t  btResCode;
  byte_t  abtId[8];
  byte_t  abtPad[8];
  byte_t  abtSysCode[2];
} nfc_felica_info_t;

/**
 * @struct nfc_iso14443b_info_t
 * @brief NFC ISO14443B tag information
 */
typedef struct {
/** abtPupi store PUPI contained in ATQB (Answer To reQuest of type B) (see ISO14443-3) */
  byte_t abtPupi[4];
/** abtApplicationData store Application Data contained in ATQB (see ISO14443-3) */
  byte_t abtApplicationData[4];
/** abtProtocolInfo store Protocol Info contained in ATQB (see ISO14443-3) */
  byte_t abtProtocolInfo[3];
/** ui8CardIdentifier store CID (Card Identifier) attributted by PCD to the PICC */
  uint8_t ui8CardIdentifier;
} nfc_iso14443b_info_t;

/**
 * @struct nfc_jewel_info_t
 * @brief NFC Jewel tag information
 */
typedef struct {
  byte_t  btSensRes[2];
  byte_t  btId[4];
} nfc_jewel_info_t;

/**
 * @union nfc_target_info_t
 * @brief Union between all kind of tags information structures.
 */
typedef union {
  nfc_iso14443a_info_t nai;
  nfc_felica_info_t nfi;
  nfc_iso14443b_info_t nbi;
  nfc_jewel_info_t nji;
  nfc_dep_info_t ndi;
} nfc_target_info_t;

/**
 * @enum nfc_baud_rate_t
 * @brief NFC baud rate enumeration
 */
typedef enum {
  NBR_UNDEFINED = 0,
  NBR_106,
  NBR_212,
  NBR_424,
  NBR_847,
} nfc_baud_rate_t;

/**
 * @enum nfc_modulation_type_t
 * @brief NFC modulation type enumeration
 */
typedef enum {
  NMT_ISO14443A,
  NMT_ISO14443B,
  NMT_FELICA,
  NMT_JEWEL,
  NMT_DEP,
} nfc_modulation_type_t;

/**
 * @struct nfc_modulation_t
 * @brief NFC modulation structure
 */
typedef struct {
  nfc_modulation_type_t nmt;
  nfc_baud_rate_t nbr;
} nfc_modulation_t;

/**
 * @struct nfc_target_t
 * @brief NFC target structure
 */
typedef struct {
  nfc_target_info_t nti;
  nfc_modulation_t nm;
} nfc_target_t;

// Reset struct alignment to default
#  pragma pack()

#endif // _LIBNFC_TYPES_H_
