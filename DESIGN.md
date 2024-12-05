## T
* USER decides to join payed tier
  * USER_ID is used to track USER (correlation id)
* SUBSCRIPTION is created for USER
  * Subscription contains plan, addons and configuration (dates, periods)
* PRICING_ENGINE is used to calculate price for each period
* SUBSCRIPTION generates individual PAYMENT for each PERIOD and tracks execution status
* For each PAYMENT multiple TRANSACTIONS can be generated (in case of payment rejections)

* Transaction is a single proces of charging USER amount based on Subscription
* Payment represents proces and status of executing charge for each period
* In order to execute Payment, multiple transaction could be made
* List of Payments and their status is tracked in Subscription