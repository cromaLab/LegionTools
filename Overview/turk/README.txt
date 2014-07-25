mTurk PHP API README
--------------------

Documentation for this API at Amazon can be found at:
http://docs.amazonwebservices.com/AWSMechanicalTurkRequester/2006-08-23/

See GUIDE.txt for a quick user guide for the API, or REFERENCE.txt for a
more detailed reference guide.

Known bugs/problems:

* ResponseGroup support is a bit shaky, but works for single
  group types. This will be fixed in a future release.
* SOAP support - We're currently utilizing hand-coded SOAP to make those
  requests that require it. We may either switch to complete REST only
  support (when we are sure it works in 100% of cases), or utilize the
  PEAR SOAP libraries in the future.

API Example Usage (Get reviewable HITs):

---snip---
  $mt = new mTurkInterface($YourAccessKey, $YourSecretKey); /* Create interface */
  $mt->SetOperation("GetReviewableHITs"); /* Set operation */
  $mt->Status     = "Reviewable"; /* Reviewable HITs only */
  $mt->PageSize   = 100;
  $mt->PageNumber = 1;
  $mt->Invoke(); /* Attack! */

  /* Assuming success for simplicity... */
  $hits = $mt->PullHITList();
  /* $hits now contains array of HIT IDs to fetch with a GetHIT operation */
---snip--- 

Enjoy!

- Rob Beckett, Santa Cruz Tech (subwolf@gmail.com)