# UMIS - The Units of Measure Interoperability Service

<p style="text-align: justify"><em>This is the CakePHP Code for the UMIS website available at https://umis.stuchalk.domains.unf.edu. It 
does not contain the MySQL database with the idea that you will want to deploy this for your own units. However, the DB 
schema can be found [here](umisdb.zip).</em></p>

<p style="text-align: justify">In the move toward big data applications there are many activities focused on the structure, presentation, 
and annotation all kinds of data.  Central though to all these efforts is the need to report a unit with
any measured value.  Currently, there are a number of disparate activities focused around the digital
representation of units because of the urgent need to definitively represent and refer to scientific units,
quantities and dimensions in the digital space. Interoperability is significantly hampered in the current,
fragmented digital unit landscape. This project is focused on development of a site dedicated to the
interoperability, usage and documentation of unit representations.</p>

### The goals of this project are to:
- Provide a stable, authoritative source for scientific units for digital applications
- Allow international units-related bodies to facilitate the process of digital unit development
- Codify, through ontologies, vocabularies and naming conventions unit and quantity representations
- Promote systematic unit/quantity application and usage through best practices and use cases
- Allow international standards agencies to provide formal language translations of units
- Provide a mechanism whereby legacy units can be represented and related to current units
- Produce a global network of synchronized unit repositories

<p style="text-align: justify">The Units of Measure Interoperability Service (UMIS) is focused on resources and services to all data to be
annotated with unit representations (strings, vocabulary terms, or ontological definitions) such that
the interoperability, comparability, and useability of data can be significantly improved.</p>

<p style="text-align: justify">This site is designed to be integrated into any webservice (using the API) that has a need to check the
validity of a unit representation, choose the most appropriate representation of a unit, convert numeric
values based on units, access physical constants, and provide disambiguation (if needed).</p>

<p style="text-align: justify">This project is also in support of the findability, accessibility, reproducibility, and reuseability (FAIR)
of data and as such is being implemented in accordance with the GO BUILD option of 
<a href="https://www.dtls.nl/fair-data/go-fair/" rel="follow" target="_blank">GO FAIR</a>.</p>

### Global Metrology Resources
- <a href="https://www.bipm.org" rel="follow" target="_blank">BIPM (metre convention)</a>
- <a href="https://www.bipm.org/en/committees/ci/cipm/wg/cipm-tg-dsi" rel="follow" target="_blank">CIPM TG on the Digital SI</a>
- <a href="https://physics.nist.gov/cuu/Units/index.html" rel="follow" target="_blank">NIST/CODATA Units</a>
- <a href="https://codata.org/initiatives/task-groups/drum/" rel="follow" target="_blank">CODATA DRUM Taskgroup</a>

### Common Unit Representations</h4>
- <a href="http://vocab.nerc.ac.uk/collection/P06/current/" rel="follow" target="_blank">BODC</a> (oceanography)</li>
- <a href="https://cdd.iec.ch/cdd/iec61360/iec61360.nsf/Units" rel="follow" target="_blank">IEC</a> (electrical)</li>
- <a href="http://www.ivoa.net/documents/VOUnits/index.html" rel="follow" target="_blank">IVOA</a> (astronomy)</li>
- <a href="http://qudt.org/" rel="follow" target="_blank">QUDT</a> (general)</li>
- <a href="https://github.com/ESIPFed/sweet" rel="follow" target="_blank">SWEET</a> (earth sciences)</li>
- <a href="https://ucum.nlm.nih.gov/" rel="follow" target="_blank">UCUM</a> (medicine)</li>
- <a href="http://www.unidata.ucar.edu/software/udunits/" rel="follow" target="_blank">UDUnits</a> (atmospheric sciences)</li>
- <a href="https://unece.org/trade/cefact/UNLOCODE-Download" rel="follow" target="_blank">UNECE</a> (internal trade)</li>
- <a href="https://bioportal.bioontology.org/ontologies/UO" rel="follow" target="_blank">UO</a> (biology)</li>

This <a href="files/Semantic-Units-Project.pdf">project</a> was funded by NIST through award #70NANB17H209 (2017-2021)
