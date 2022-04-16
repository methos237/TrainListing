# TrainListing
Coding Exercise for Wellspring

James Knox Polk 
[james.knox.polk.cs@gmail.com](mailto:james.knox.polk.cs@gmail.com)

# Installation
 * Clone repo to applicable htdocs folder
 * run ```$ composer install``` from the doc root
 * SQL script for creating the 'trains' table is ```trains.sql```
 * Modify ```src/db.conf.php``` with appropriate SQL server information
 * Navigate to ```index.php``` in browser 

# Notes on Implementation
 * I have used my own custom Component framework to implement the MVC architecture
   * All files of this framework are in the ```src/ComponentSystem``` folder
   * I did not include the routing implementation since it was not needed for this single-page project
   * This was done to speed up the proceess of creating a framework
   * Hopefully this meets the requirements of not using an out-of-the-box framework as I created this myself

 * Tests reside in the ```tests/``` folder and include tests for the CSV parser and the Train model
  * The CSV file that I was given for the exwecise is in the ```tests/data``` folder
    * ***Please note*** that the CSV file that I was given is missing the Route for Run Number M623. This is not a file parsing error.

# The Requirements

## Introduction 

There are many train lines in the city of Chicago. The El is the quickest, the Metra is used to and from the suburbs, and the Amtrak crosses large distances. Your assignment is to write a program that reads in a ‘comma separated values’ (CSV) file containing train information and outputs the data to the user. 

## Input Details 
* We have provided a CSV file with sample data in the following format: 
  * TRAIN_LINE, ROUTE_NAME, RUN_NUMBER, OPERATOR_ID 
  * El, Brown Line, E102, SJones 
  * Metra, UPN, M405, AJohnson 
  * Amtrak, Hiawatha, A006, LBeck 
* Each row of the CSV will contain data for the following fields: 
  * Train Line (El, Metra, Amtrak) 
  * Route Name (i.e., Brown Line) 
  * Run Number 
  * Operator ID 
* The first row of the CSV will always be a header row specifying the field names for each of the columns 
* Each line of the CSV will end with a combination of a carriage return and a line feed: \r\n 

## Example of Expected Output 
<table>
  <tr>
    <th>Train Line</th>
    <th>Route</th>
    <th>Run Number</th>
    <th>Operator ID</th>
  </tr>
  <tr>
    <td>El</td>
    <td>Brown Line</td>
    <td>E102</td>
    <td>SJones</td>
  </tr>
  <tr>
    <td>Metra</td>
    <td>UPN</td>
    <td>M405</td>
    <td>AJohnson</td>
  </tr>
  <tr>
    <td>Metra</td>
    <td>UPN</td>
    <td>M511</td>
    <td>YSmith</td>
  </tr>
  <tr>
    <td>Amtrak</td>
    <td>Hiawatha</td>
    <td>A006</td>
    <td>LBeck</td>
  </tr>
  <tr>
    <td>El</td>
    <td>Red Line</td>
    <td>E432</td>
    <td>LHill</td>
  </tr>
  <tr>
    <td>Amtrak</td>
    <td>Hiawatha</td>
    <td>A005</td>
    <td>LBeck</td>
  </tr>
</table>

## Additional Requirements 
- [x] Allow users to upload the CSV file 
- [x] Display the data in the specified format as the content of a web page 
- [x] All entries displayed must be unique 
- [x] Output should be sorted in alphabetical order by Run Number

## Bonus 
- [ ] Add pagination controls which show 5 valid data items per page 
- [ ] Add sorting by any column 
- [x] Set up CRUD (Create, Read, Update, Delete) functionality 
- [ ] Set up hosting and provide a link along with your code 
