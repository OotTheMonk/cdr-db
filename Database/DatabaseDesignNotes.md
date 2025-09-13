# Database Design Notes

## Notes and Assumptions

1. **Primary Key and Receive Date**: There is a separate `uniqueId` as the primary key and receive date, as each entry in this table is philosophically the reception of a CDR, not simply a list of normalized CDRs. The database could be designed differently depending on how we would want to handle the possibility of receiving duplicate CDRs. I made the assumption that it would be a possibility and started designing the database with that in mind.
2. **Soft Delete Field**: The soft delete field exists to support basic record deletion functionality. In most cases, destructive user actions should be reversible. Soft deletion supports that. The alternative would be to delete the record from this table and add it to a soft deleted table, so it would be removed from the operational index. For the purposes of this project, I have chosen simplicity. Depending on scale, it may be desirable to move deleted records to a different table. If there are specific legal requirements, it's possible soft deletion is not possible and it should be hard deleted immediately (e.g., GDPR right to be forgotten). With a user authentication system in place, we would also want to audit who deleted records and when.
3. **IP Field Length**: The `ip` field was given a length of 64 to accommodate the possibility of receiving an IPv6 address in the future.
4. **DMCC Field Length**: The `dmcc` field was made very long due to uncertainty of what it represented. Basic research suggests it could be vendor-specific Avaya Device, Media, and Call Control. Some Avaya CDR fields appear to possibly be quite long, so a long value was chosen for the purposes of this project.

## Sources

- [Avaya Device, Media, and Call Control Service Documentation](https://documentation.avaya.com/bundle/AdministeringApplicationEnablementServicesForAvayaContactCenterExtendedCapacity_r102/page/Device_Media_and_Call_Control_service.html)
- [Avaya Call Detail Recording (CDR) System Parameters](https://documentation.avaya.com/bundle/CMScreenReference_R10.1/page/system_parameters_cdr.html)
