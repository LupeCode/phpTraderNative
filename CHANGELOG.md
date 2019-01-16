# Change Log

## 1.2.0
  * Added the static variable to the test. Use this instead of a string just in case the string for the errors change.
  * Test all of the MA types that stochrsi supports.
  * Added some test data that matches [Issue #2](https://github.com/LupeCode/phpTraderNative/issues/2).
  * Added the Lupe Trader classes.
    * These classes will override certain functions that have been identified to produce bad results.
    * See [Issue #2](https://github.com/LupeCode/phpTraderNative/issues/2).
  * Wrote tests with hand-done math to verify that the returned results match what it should be.
    * There were already tests that made sure the results matched the PECL Trader extension, but what if that was wrong?
    * I didn't to it by hand *per se*, but in a spreadsheet. I'm not *that* good at math.
  * Added the Slow Stoch RSI function to solve [Issue #2](https://github.com/LupeCode/phpTraderNative/issues/2).
  * Moved the common testing data into a trait for classes that cannot extend the `TraderTest` class.
  * Added the `.gitlab-ci.yml` and `.travis.yml` files.
    * Hey, who can say no to free code testing and reporting, right?
## 1.1.0
  * Removed the MyInteger class, using plain integers now.
  * Made the return message constant and easier to test against.
  * Switched to using the core classes in a static way. This will improve operating time when the same class of functions are used multiple times in one execution.
  * Added tests that cause coverage to hang with a group that can be skipped with `--exclude-group exceptions`
  * Updated the static references.
## 1.0.5
  * Added an installation notice to the README.
## 1.0.4
  * Fixed [Issue # 1](https://github.com/LupeCode/phpTraderNative/issues/1)
  * Shortened the code for htDcPeriod.
## 1.0.3
  * Added a test that shows that the PECL version and the C/JAVA versions of TA-Lib have different defaults.
  * Added testing for the TraderFriendly class.
  * Changed the version requirement of PECL Trader to 0.4.1 for require-dev.
  * Removed unused internal functions.
## 1.0.2
  * Updated for PECL Trader version 0.4.1.
  * Optimized the code.
  * Made sure that the original copyright notice was in all of the ported code.
  * Set up local tests to be in multiple versions of PHP; all versions from 7.0.x to 7.2.x.
## 1.0.1
  * Removed the old java code that was unused.
## 1.0.0
  * Lots and lots of function documentation.
  * Moved the optional parameters in to the function definitions instead of in the body of the function.
## 0.4.0
  * Finished all of the classes; the library now has the same functionality as the TA-Lib code it is ported from.
## 0.3.0
  * Set up the basic scaffolding of the library.
