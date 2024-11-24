/**
 * @async
 * @function
 * @name getAll
 * @kind function
 * @returns {Promise<any>}
 * @exports
 */
export async function getAll() {
    let response = await fetch('http://localhost:8010/Api/UserApi.php?id=All');
    return response.json();
}