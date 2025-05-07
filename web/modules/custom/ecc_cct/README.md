# Essex County Council cookie consent statistics.

This module hooks into the events thown by the EU Cookie Compliance module.

Whenever consent is set, this module takes a simplified value of choice: i.e.
granted or denied, and sends it to an endpoint.

The endpoint controller writes the time and value to a custom table.

An administrator can access a simple aggregate report of choices. It calculates
the percentage of grants out of the total number of records.
