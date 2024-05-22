Almost enterprise grade transactions text file reader. Maybe I'd add K8s configuration in the future.
Or parallel processing.

Usage 
```bash
./comission-calculator process:file dev/Unit/TransactionReader/input.txt
```

Because of the requests limits on the external services (3 requests for 1 hour for the lookup.binlist.net), most probably it would not work for you.
Until you asked them to whitelist your IP address.