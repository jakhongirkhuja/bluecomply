<?php

namespace App\Services\I3Screen;

use App\Models\Company\DotAgency;
use App\Models\Company\DrugTestOrder;
use App\Models\General\States;

class I3OrderXmlBuilder
{
    public static function build(DrugTestOrder $order, $driver): string
    {

        $observed = $order->observed? 1 :0;
        $dot_agency_name = DotAgency::find($order->dot_agency)->value('code');
        $state_id = 'US';
        $licenceNumber = '';
        if($driver->license){
            $state_id =States::find($driver->license->state_id)->value('state_id');
            $licenceNumber = $driver->license->number;
        }
        return <<<XML
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
             <soapenv:Body>
              <BackgroundCheck>
               <userId>{config('i3.user')}</userId>
               <password>{config('i3.password')}</password>
               <xmlstring><![CDATA[
                <BackgroundCheck account="{config('i3.account')}">
                  <BackgroundSearchPackage>

                    <ReferenceId>
                      <IdValue>{$order->reference_id}</IdValue>
                    </ReferenceId>

                    <PersonalData>
                      <PersonName>
                        <GivenName>{$driver->first_name}</GivenName>
                         <MiddleName>{$driver->middle_name}</MiddleName>
                        <FamilyName>{$driver->last_name}</FamilyName>

                      </PersonName>

                      <DemographicDetail>
                        <GovernmentId countryCode="{$state_id}" issuingAuthority="CDL">
                         {$licenceNumber}
                        </GovernmentId>
                        <DateOfBirth>{$driver->date_of_birth}</DateOfBirth>
                      </DemographicDetail>

                      <ContactMethod>
                        <InternetEmailAddress>{$driver->personal_information?->email}</InternetEmailAddress>
                      </ContactMethod>
                    </PersonalData>

                    <Screenings>
                      <Screening type="drug">
                        <SearchDrugs>
                          <ReasonForTest>{$order->reason}</ReasonForTest>
                          <TestType packageCode="000123abc" />
                        </SearchDrugs>
                      </Screening>
                    </Screenings>

                    <OtherApplicantInformation>
                      <IdValue name="DOTAgency">{$dot_agency_name}</IdValue>
                      <IdValue name="observedCollectionRequired">
                        {$observed}
                      </IdValue>
                    </OtherApplicantInformation>

                    <CollectionDate dateDescription="expirationDate">
                      <AnyDate>{$order->expiration_date}T23:59:59</AnyDate>
                    </CollectionDate>

                    <AdditionalItems type="Informational" qualifier="note">
                      <Text>{$order->notes}</Text>
                    </AdditionalItems>

                  </BackgroundSearchPackage>
                </BackgroundCheck>
               ]]></xmlstring>
              </BackgroundCheck>
             </soapenv:Body>
            </soapenv:Envelope>
            XML;
    }
}
