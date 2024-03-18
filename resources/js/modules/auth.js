async function logUserIn(username, password, remember_me) {
    try {
        const res = await axios.post('/api/auth/login',
            {
                username: username,
                password: password,
                remember_me: remember_me,
            }
        );
        return res;
    } catch (error) {
        throw error;
    }
}
async function logUserOut() {
    try {
        const res = await axios.post('/api/auth/logout');
        return res;
    } catch (error) {
        throw error;
    }
}
async function isAuthorized() {
    try {
        const res = await axios.post('/api/auth/verify');
        return res;
    } catch (error) {
        if (error.response.status === 401) {
            return false;
        }
        throw error;
    }
}
async function changeUserPassword(current_pass, new_pass, confirm_pass) {
    try {
        const res = await axios.patch('/api/user/password',
            {
                current_pass: current_pass,
                new_pass: new_pass,
                confirm_pass: confirm_pass,
            }
        );
        return res;
    } catch (error) {
        throw error;
    }
}
export { logUserIn, logUserOut, isAuthorized, changeUserPassword };