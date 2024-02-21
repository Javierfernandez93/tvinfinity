<?php

namespace JFStudio;

class CoinPayments {
    const PUBLIC_KEY = '6b9036954029b6141ce47d1f08ac7a3a47acec701f65ca9bf9012759bf9747ea';
    const PRIVATE_KEY = '33E99C21B350BD7BD57af0A6421a17086d9088373a58D2c31A54C79F093c002A';
    const MERCHANT_ID = 'd8c1f403c780e4c0acb5e9e013009cf8';
    const IPN_SECRET = 'cb5e9e013009cf8@ZUUMMYIPNSECRET@cb5e9e013009cf8';

    //* status * */
    const COMPLETE = 100;
    const INCOMPLETE = 0;
    const EXPIRED = -1;
}