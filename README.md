# Membership Offer module

This module provides a "Membership Offer" entity type to integrate with the
Drupal 8 Membership module.

A "Membership Offer" is a purchasable entity which serves to provide a target for
Drupal Commerce order items. An offer defines a target membership type (bundle)
and may store other site-specific data such as benefits, term lengths and the like
that may be stored on fields. The offer which was initially redeemed to create the
membership is stored on the membership, as a back reference.
