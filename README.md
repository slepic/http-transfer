[![Build Status](https://travis-ci.org/slepic/http-transfer.svg?branch=master)](https://travis-ci.org/slepic/http-transfer)
[![Style Status](https://styleci.io/repos/184416277/shield)](https://styleci.io/repos/184416277)

# http-transfer
Simple PHP library working with PSR HTTP message transfers.

## Usage

There are 3 components at this moment:

### Log

Simple structure holing the request, response, error, time of start and end of the transfer.

### Observer

An abstraction over the transfer process where the observer is notified about start and end of transfer processes.

### History

A coposition of the two above that implements the osbserver to create the logs with duration information.
