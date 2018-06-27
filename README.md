ZEJBM
===

Ez a negyedik változat, ami a témához készült. Az előző pár nem tetszett, sok hibás megközelítést 
alkalmaztam (inside-out tdd consumer oldalról és rich model volt túl sok pure function -- curryvel, de php nem tud callback-re typehintelni).

Az utóbbi időben csak typescript + haskell funkcionális programozok és az utóbbi évben totál nem nyúltam php-hez :(

Szerettem volna DDD-vel készíteni, de két napi nem alvás után már 
alig látok. 
Nem az a feladat, amihez dukál a DDD, de gyakorlásnak jó megközelítésnek gondoltam.  


Bármilyen command / query szeparáció overhead. A use-case és a repository elég kéne, hogy legyen.

Ami kimaradt:
- logolás/backend (sima logger service, iface mögött, adogatom át constructorban. egyből ment sqlite-ba repository-n át)
- logolás/frontend (ajax, GET /logs?since=date)
- email küldés (sima mail fgv egy svc mögött, a use-case-ből hívva)
- tényleges szeparáció context mentén (share -t export)
- test env-en kívül máshoz config (és ezek emiatti betöltésének kiszervezése)
- bootstrap / setup / install 
    - docker-compose update
    - command a cli php futtatáshoz
    - command a web szerver futtatáshoz

Behat tesztek:
- logolás miatt kell goutte mellé/helyett másik driver (zombie js?!)
- 1:3 fail arány teszteléshez (fake használatával)
- 3. fail esetén eltűnik a message (fake használatával)

Amin változtatnék:
- behat context share -re temérdek verzió óta nincs lehetőség, a trait-ek pedig nevetségesek (de ezt a ajánlják wut)
- features helye


Setup
===

```
$ docker-compose up -d
$ docker-compose exec app sh
/ # cd /app
/app # vendor/bin/phpunit
/app # vendor/bin/
```

FAQ
===

Q: Nem található xy class  
A: 
 - `docker-compose run composer install --ignore-platform-reqs`
 - `docker-compose run composer dump-autoload`  

Q: Nem éri el / nem tudja írni / nem tud paramétert változtatni az xy queue(n)  
A: `docker-compose kill rabbitmq && docker-compose up rabbitmq -d`  

Q: Miért nem fejezted be?  
A: Borzasztó sok energiát öltem bele és szarul is érzem magam miatta   
 