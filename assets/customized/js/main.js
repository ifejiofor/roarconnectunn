var textToDisplayInButtonThatWillTellUserToLogInBeforeContinuing = [];
var miscellaneousAttributesOfButtonThatWillTellUserToLogInBeforeContinuing = [];

var namesOfAllDessertsSoldByVendor = [];
var MAXIMUM_QUANTITY_OF_DESSERT = 10;
var numberOfDessertsSelectedByUserSoFar = 0;

function displayDessertsPreviouslySelectedByUser( namesOfDessertsThatWhereSelectedByUser, quantitiesOfDessertsThatWhereSelectedByUser )
{

   var currentIndex, j, quantity;
   var status;
   var numberOfSelectedDesserts;
   var markupContainingDesserts;

   if ( namesOfDessertsThatWhereSelectedByUser.length != quantitiesOfDessertsThatWhereSelectedByUser.length || 
      namesOfDessertsThatWhereSelectedByUser.length == 0 )
   {
      return;
   }

   numberOfSelectedDesserts = namesOfDessertsThatWhereSelectedByUser.length;
   markupContainingDesserts = 
         '<legend class="text-center" id="boldSmallSizedText">Extras to be added to order delivery:</legend>\n' +
         '<p class="text-center"><button class="btn btn-primary btn-sm" id="buttonForSelectingAnotherDessert">Click Here to Add Another Extras</button></p>\n' +
         '<input type="hidden" name="atLeastOneDessertWasSelectedByUser" value="true" />\n\n';

   for ( currentIndex = 0; currentIndex < numberOfSelectedDesserts; currentIndex++ ) {
      markupContainingDesserts += 
            '<div class="col-sm-6">\n' +
               '<label for="nameOfDessert' + currentIndex  + '" class="col-sm-6 control-label">Select Extras:</label>\n' +
               '<div class="col-sm-6">\n' +
                  '<select name="nameOfDessert' + currentIndex + '" class="form-control" id="nameOfDessert' + currentIndex  + '">\n' +
                     '<option value="">---</option>\n';

      for ( j = 0; j < namesOfAllDessertsSoldByVendor.length; j++ ) {
         if ( namesOfAllDessertsSoldByVendor[j] == namesOfDessertsThatWhereSelectedByUser[currentIndex] ) {
            status = ' selected';
         }
         else {
            status = '';
         }

         markupContainingDesserts += 
                     '<option value="' + namesOfAllDessertsSoldByVendor[j] + '" ' + status + '>' + namesOfAllDessertsSoldByVendor[j] + '</option>\n';
      }

      markupContainingDesserts +=
                  '</select>\n' +
               '</div>\n' +
            '</div>\n' +

            '<div class="col-sm-6">\n' +
               '<label for="quantityOfDessert' + currentIndex  + '" class="col-sm-6 control-label">Select quantity:</label>\n' +
               '<div class="col-sm-6">\n' +
                  '<select name="quantityOfDessert' + currentIndex + '" class="form-control" id="quantityOfDessert' + currentIndex  + '">\n';

      for ( quantity = 1; quantity <= MAXIMUM_QUANTITY_OF_DESSERT; quantity++ ) {
         if ( quantity == quantitiesOfDessertsThatWhereSelectedByUser[currentIndex] ) {
            status = ' selected';
         }
         else {
            status = '';
         }

         markupContainingDesserts += 
                  '<option value="' + quantity + '" ' + status + '>' + quantity + '</option>\n';
      }

      markupContainingDesserts +=
               '</select>\n' +
            '</div>\n' +
         '</div>\n\n';
   }

   document.getElementById( 'sectionOfFormMeantForAllowingUserToSelectDessert' ).innerHTML = markupContainingDesserts;
   document.getElementById( 'buttonForSelectingAnotherDessert' ).addEventListener( 'click', displayFormFieldsThatAllowUserToSelectAnotherDessert );

   numberOfDessertsSelectedByUserSoFar = numberOfSelectedDesserts;
}


function displayFormFieldsThatAllowUserToSelectAnotherDessert()
{
   var markupContainingFormField = 
            '<div class="col-sm-6">\n' +
               '<label for="nameOfDessert' + numberOfDessertsSelectedByUserSoFar  + '" class="col-sm-6 control-label">Select Extras:</label>\n' +
               '<div class="col-sm-6">\n' +
                  '<select name="nameOfDessert' + numberOfDessertsSelectedByUserSoFar + '" class="form-control" id="nameOfDessert' + numberOfDessertsSelectedByUserSoFar  + '">\n' +
                     '<option value="">---</option>\n';

      for ( j = 0; j < namesOfAllDessertsSoldByVendor.length; j++ ) {
         markupContainingFormField += 
                     '<option value="' + namesOfAllDessertsSoldByVendor[j] + '">' + namesOfAllDessertsSoldByVendor[j] + '</option>\n';
      }

      markupContainingFormField +=
                  '</select>\n' +
               '</div>\n' +
            '</div>\n' +

            '<div class="col-sm-6">\n' +
               '<label for="quantityOfDessert' + numberOfDessertsSelectedByUserSoFar  + '" class="col-sm-6 control-label">Select quantity:</label>\n' +
               '<div class="col-sm-6">\n' +
                  '<select name="quantityOfDessert' + numberOfDessertsSelectedByUserSoFar + '" class="form-control" id="quantityOfDessert' + numberOfDessertsSelectedByUserSoFar  + '">\n';

      for ( quantity = 1; quantity <= MAXIMUM_QUANTITY_OF_DESSERT; quantity++ ) {
         markupContainingFormField += 
                  '<option value="' + quantity + '">' + quantity + '</option>\n';
      }

      markupContainingFormField +=
               '</select>\n' +
            '</div>\n' +
         '</div>\n\n';

   if ( numberOfDessertsSelectedByUserSoFar == 0 ) {
      markupContainingLegendAndButtonAndHiddenInputField =
         '<legend class="text-center" id="boldSmallSizedText">Extras to be added to order delivery:</legend>\n' +
         '<p class="text-center"><button class="btn btn-primary btn-sm" id="buttonForSelectingAnotherDessert">Click Here to Add Another Extras</button></p>\n' +
         '<input type="hidden" name="atLeastOneDessertWasSelectedByUser" value="true" />\n\n';

      document.getElementById( 'sectionOfFormMeantForAllowingUserToSelectDessert' ).innerHTML = 
         markupContainingLegendAndButtonAndHiddenInputField + markupContainingFormField;
   }
   else {
      var i, name, quantity;
      var backupOfNamesOfDessertsSelectedSoFar = [];
      var backupOfQuantitiesOfDessertsSelectedSoFar = [];

      for ( i = 0; i < numberOfDessertsSelectedByUserSoFar; i++ ) {
         name = document.forms['formForOrderingFood']['nameOfDessert' + i].value;
         quantity = document.forms['formForOrderingFood']['quantityOfDessert' + i].value;

         backupOfNamesOfDessertsSelectedSoFar.push( name );
         backupOfQuantitiesOfDessertsSelectedSoFar.push( quantity );
      }

      document.getElementById( 'sectionOfFormMeantForAllowingUserToSelectDessert' ).innerHTML += markupContainingFormField;

      for ( i = 0; i < backupOfNamesOfDessertsSelectedSoFar.length; i++ ) {
         document.forms['formForOrderingFood']['nameOfDessert' + i].value = backupOfNamesOfDessertsSelectedSoFar[i];
         document.forms['formForOrderingFood']['quantityOfDessert' + i].value = backupOfQuantitiesOfDessertsSelectedSoFar[i];
      }
   }

   document.getElementById( 'buttonForSelectingAnotherDessert' ).addEventListener( 'click', displayFormFieldsThatAllowUserToSelectAnotherDessert );

   ++numberOfDessertsSelectedByUserSoFar;
}


function displayButtonForSelectingAnInitialDessert()
{
   var markupContainingButton =
               '<legend class="text-center" id="boldSmallSizedText">(Optional) Will you like to add extras (e.g., meat, fish, salad, e.t.c.) to your order?</legend>\n' +
               '<p class="text-center">If yes, then <button class="btn btn-primary btn-sm" id="buttonForSelectingAnotherDessert">Click Here to Add Extras</button></p>\n';

   document.getElementById( 'sectionOfFormMeantForAllowingUserToSelectDessert' ).innerHTML += markupContainingButton;
   document.getElementById( 'buttonForSelectingAnotherDessert' ).addEventListener( 'click', displayFormFieldsThatAllowUserToSelectAnotherDessert );

   numberOfDessertsSelectedByUserSoFar = 0;
}